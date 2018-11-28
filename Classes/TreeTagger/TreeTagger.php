<?php
namespace RKW\RkwSearch\TreeTagger;
use RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\Helper\Text;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Class TreeTagger
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class TreeTagger {


    /**
     * @var \RKW\RkwSearch\OrientDb\Cache\TreeTaggerCache
     * @inject
     */
    protected $cache;


    /**
     * @string Path to TreeTagger
     */
    protected $absolutePathToTagger;


    /**
     * @integer Language Uid
     */
    protected $languageUid;


    /**
     * @var array Contains the TypoScript settings
     */
    protected $settings;


    /**
     * @var array Contains the scripts
     */
    protected $scripts;


    /**
     * @var array Contains the options for the scripts
     */
    protected $options;


    /**
     * @var string Contains the text to process
     */
    protected $text;


    /**
     * @var Array Contains the results from TreeTagger
     */
    public $results;


    /**
     * Gets text to process
     *
     * @return string
     */
    public function getText () {

        return $this->text;
        //===
    }



    /**
     * Gets processed text consisting of word-bases
     *
     * @param string $separator
     * @return string
     */
    public function getTextStemmed($separator = ' ') {

        $result = '';
        foreach ($this->getResults() as $element) {
            if ($element instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord) {
                if (
                    ($result)
                    && (strpos($element->getTag(), '$') === FALSE)
                ) {
                    $result .= ' ' . $element->getBase();
                } else {
                    $result .= $element->getBase();
                }
            }
        }

        return $result;
        //===
    }



    /**
     * Sets text to process
     *
     * @param string $value
     * @param array|string $configuration If a separate configuration for sanitizeString is need, you can pass it here to force sanitizeString to use its own configuration!
     * @return $this
     */
    public function setText ($value, $configuration = array()) {

        if (! $configuration)
            $configuration = $this->getConfiguration('textFilterRegExpr');

        // strip HTML and replace entities by the original values!
        $this->text = Text::sanitizeString($value, $this->languageUid, $configuration);
        return $this;
        //===
    }


    /**
     * Returns result
     *
     * @return \RKW\RkwSearch\TreeTagger\Collection\Records
     */
    public function getResults () {

        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\TreeTagger\\Collection\\Records', $this->results);
        //===
    }


    /**
     * Filters the returned data
     *
     * @param string $filter
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    public function getFilteredResults($filter) {

        // get names of class
        $modelName = 'RKW\\RkwSearch\\TreeTagger\\Filter\\' . ucfirst(strtolower($filter));

        // check if classes exist
        if (! class_exists($modelName))
            throw new \RKW\RkwSearch\Exception(sprintf('Filter with name "%s" was not found.', $filter), 1415784117);
            //===

        $filterModel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($modelName, $this->getResults(), $this->getConfiguration('filter'));
        return $filterModel->execute();
        //===

    }


    /**
     * Executes tagging
     *
     * @return $this
     * @throws \RKW\RkwSearch\Exception
     */
    public function execute () {

        $this->results = array ();
        if (
            ($text = $this->getText())
            && ($execCode = $this->getExecutableCode())
        )  {

            foreach ($this->getSplitText() as $textRow) {

                // since escapeshellarg corrupts utf8 we use this construction here
                $tempResult = array ();
                $escapedArg = "'" . str_replace("'", "'\\''", $textRow) . "'";
                $finalCode = 'echo ' . $escapedArg . ' | ' . str_replace('{PATH}', $this->absolutePathToTagger, $execCode);
                if (
                    (! exec($finalCode, $tempResult))
                    || (empty($tempResult))
                    || (!is_array($tempResult))
                ) {
                    throw new \RKW\RkwSearch\Exception('Error while trying to tag the given text. Could not execute the tag-commands. Please check execution rights for folders `cmd` and `bin`. Command: `' . $finalCode . '`', 1401116578);
                    //===
                }

                $this->results = array_merge ($this->results, $tempResult);
            }
        }


        return $this;
        //===
    }


    /**
     * Executes tagging
     *
     * @param integer $maxLength (should not be longer than 4096!)
     * @param string $separator
     * @return array
     */
    public function getSplitText ($maxLength = 4000, $separator = ' ') {

        if ($text = $this->getText())  {

            $wrappedText = wordwrap ($text, $maxLength, '###BREAK###', FALSE);
            return explode('###BREAK###', $wrappedText);
            //===
        }

        return array();
        //===
    }


    /**
     * Returns the executable code as string
     *
     * @param boolean $default Only load defaults
     * @return string
     * @throws \RKW\RkwSearch\Exception
     */
    public function getExecutableCode($default = TRUE) {

        $scripts = $this->getScripts($default);
        $options = $this->getOptions($default);

        $code = array();
        if (
            ($scripts['tokenizer'])
            && ($scripts['abbrList'])
        )
            $code[] = '{PATH}/' . escapeshellcmd($scripts['tokenizer']) . ' -a {PATH}/' . escapeshellcmd($scripts['abbrList']) . ' $*';

        if ($scripts['lexFile'])
            $code[] = 'perl {PATH}/cmd/lookup.perl' . ' {PATH}/' . escapeshellcmd($scripts['lexFile']);

        if (
            ($scripts['tagger'])
            && ($scripts['parFile'])
        )
            $code[] = '{PATH}/' . escapeshellcmd($scripts['tagger']) .  (($options['options']) ? ' ' . escapeshellcmd($options['options']) : '') . ' {PATH}/' . escapeshellcmd($scripts['parFile']);

        if ($scripts['filter'])
            $code[] = '{PATH}/' . escapeshellcmd($scripts['filter']);


        return implode(' | ', $code);
        //===
    }


    /**
     * Get TypoScript configuration
     *
     * @param boolean $default Only load defaults
     * @return array
     */
    protected function getScripts($default = FALSE) {

        if (! $this->scripts) {

            // set defaults and overload with settings from TypoScript
            $this->scripts = array (
                'tokenizer' => 'cmd/utf8-tokenize.perl',
                'tagger' => 'bin/tree-tagger',
                'abbrList' => 'lib/german-abbreviations-utf8',
                'parFile' => 'lib/german-utf8.par',
                'lexFile' => 'lib/german-lexicon-utf8.txt',
                'filter' => 'cmd/filter-german-tags',
            );

            // overload with settings from TypoScript
            if (! $default)
                $this->scripts = array_merge (
                    $this->scripts,
                    $this->getConfiguration('scripts') ? $this->getConfiguration('scripts') : array()
                );
        }

        return $this->scripts;
        //===
    }


    /**
     * Get TypoScript configuration
     *
     * @param boolean $default Only load defaults
     * @return array
     */
    protected function getOptions($default = FALSE) {

        if (! $this->options) {

            // set defaults
            $this->options = array (
                'options' => '-token -lemma -sgml -pt-with-lemma'
            );

            // overload with settings from TypoScript
            if (! $default)
                $this->options = array_merge (
                    $this->options,
                    $this->getConfiguration('options') ? $this->getConfiguration('options') : array()
                );
        }

        return $this->options;
        //===
    }

    /**
     * Returns the cache object
     *
     * @return \RKW\RkwSearch\OrientDb\Cache\TreeTaggerCache
     */
    public function getCache() {

        if (! $this->cache instanceof \RKW\RkwSearch\OrientDb\Cache\TreeTaggerCache)
            $this->cache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\OrientDb\\Cache\\TreeTaggerCache');

        return $this->cache;
        //===
    }

    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getConfiguration($key = NULL) {

        if (! $this->settings) {

           // load from TypoScript
           $settings = Common::getTyposcriptConfiguration();
           if ($settings['treeTagger'])
               $this->settings = $settings['treeTagger'];
        }

        if (! $this->settings)
            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1422519937);
            //===

        if ($key) {

            if ($this->settings[$this->languageUid][$key])
                return $this->settings[$this->languageUid][$key];
                //===

            return $this->settings[0][$key];
            //===
        }

        return $this->settings;
        //===
    }


    /**
     *  Constructor
     *
     * @param integer $languageUid
     * @param array $configuration
     */
    public function __construct ($languageUid = 0, $configuration = array()) {

        // set absolute path
        $this->absolutePathToTagger = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('rkw_search') . 'Classes/Libs/TreeTagger';
        $this->languageUid = intval($languageUid);

        // set given configuration (if given)
        if (!empty ($configuration))
            $this->settings = $configuration;
    }


} 