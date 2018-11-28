<?php
namespace RKW\RkwSearch\Keywords;
use \RKW\RkwSearch\OrientDb\Helper\Common;
use \RKW\RkwSearch\Helper\Text;

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
 * Class Fetcher
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Fetcher {


    /**
     * @var array Contains the configuration from TCA
     */
    protected $configuration;


    /**
     * Get content model
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $model
     * @param string $fieldListKey
     * @returns string|NULL
     * @throws \RKW\RkwSearch\Exception
     */
    public function getContent ($model, $fieldListKey = 'fieldList') {

        if (! $model instanceof \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface)
            throw new \RKW\RkwSearch\Exception ('Data model must be instance of \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface.', 1425539517);
            //===

        if (
            ($className = Common::getShortName($model))
            && ($fieldsString = $this->getConfigurationForClass($className, $fieldListKey))
            && ($fields = explode(',', str_replace(' ', '', $fieldsString)))
        ){

            $data = array ();
            foreach($fields as $field) {
                $getter = 'get' . ucfirst($field);
                try {
                    $data[] = $model->$getter();
                } catch (\Exception $e) {
                    // do nothing
                }

            }

            if ($separator = $this->getConfigurationForClass($className, 'separator'))
                return Text::mergeToString($data, $separator);
                //===

            return Text::mergeToString($data);
            //===
        }


        return NULL;
        //===

    }


    /**
     *  Constructor
     */
    public function __construct ($configuration = array()) {

        // set given configuration (if given)
        if (!empty ($configuration))
            $this->configuration = $configuration;

        $this->getConfiguration();
    }


    /**
     * Get configuration
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getConfiguration() {

        // load from TypoScript
        if (! $this->configuration) {

            $settings = Common::getTyposcriptConfiguration();
            if ($settings['indexing'])
                $this->configuration = $settings['indexing'];
        }

        if (! $this->configuration)
            throw new \RKW\RkwSearch\Exception ('No valid setup found.', 1422951682);
            //===

        return $this->configuration;
        //===
    }


    /**
     * Get configuration fields for class
     *
     * @param string $className
     * @param string $param
     * @return string|NULL
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getConfigurationForClass($className, $param) {

        if (
            ($param)
            && ($this->configuration['fields'][$className])
            && (isset($this->configuration['fields'][$className][$param]))
        )
            return $this->configuration['fields'][$className][$param];
            //===

        return NULL;
        //===
    }

}