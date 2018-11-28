<?php

namespace RKW\RkwSearch\Helper;

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
use TYPO3\CMS\Core\Resource\File;

/**
 * Class Text
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Text {

    /**
     * Deletes stop words from string
     *
     * @param string $string
     * @param integer $languageUid
     * @param array|string $configuration
     * @return string
     */
    public static function removeStopWords ($string, $languageUid = 0, $configuration = NULL) {

        // load configuration and explode it
        if (! $configuration)
            $configuration = self::getConfiguration('stopWords', $languageUid);
        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $configuration);

        // do replacement
        if (array ($configuration)) {
            foreach ($configuration as $word)
                $string = preg_replace('/\b' . $word . '\b(\.)?/i', '', $string);
        }

        // replace doubled separators by a single one
        // variation one
        $separator = " ";
        $maxCount = 0;
        do {
            $string = str_replace($separator . $separator, $separator, $string);
            $maxCount++;
        } while (
            ($maxCount < 100)
            && (strpos($string, $separator . $separator) !== FALSE)
        );

        return trim($string);
        //===

    }

    /**
     * Converts german umlauts to normal letters
     *
     * @param string $string
     * @return string
     */
    public static function encodeGermanUmlauts ($string) {

        $search = array (
            'ä',
            'Ä',
            'ö',
            'Ö',
            'ü',
            'Ü',
            'ß'
        );

        $replace = array (
            'a',
            'A',
            'o',
            'O',
            'u',
            'U',
            'ss'
        );

        return str_replace($search, $replace, $string);
        //===

    }


    /**
     * Sanitizes given string
     *
     * @param string $value
     * @param integer $languageUid
     * @param array|string $configuration
     * @return string
     */
    public static function sanitizeString ($value, $languageUid = 0, $configuration = NULL) {

        // here we should NOT use mb_ereg_replace since it won't mtach!
        // mb_internal_encoding('UTF-8');

        // strip HTML and replace entities by the original values!
        $result = self::stripHtml($value);

        // remove links as normal text
        $result = preg_replace('/( )?(http:\/\/)?www.[äöüÖÄÜßa-z0-9\.#\/]+( )?/i', ' ', $result);

        // if nothing is given, take the defaults from TypoScript
        // if a string is given we check if there is a configuration group with that name in TypoScript
        if (! array ($configuration)) {

            $configurationTemp = self::getConfiguration('textFilterRegExpr', $languageUid);
            if (is_array($configurationTemp[$configuration])) {
                $configuration = $configurationTemp[$configuration];

            } else if (is_array ($configurationTemp['default'])) {
                $configuration = $configurationTemp['default'];
            }
        }

        if (
            ($configuration)
            && is_array($configuration)
        ){

            foreach ($configuration as $filter) {

                if ($pregExprSearch = $filter['search']) {

                    $pregExprReplace = '';
                    if ($filter['replace'])
                        $pregExprReplace = substr($filter['replace'], strpos($filter['replace'], '/')+1, strrpos($filter['replace'], '/')-1);
                    $result = preg_replace($pregExprSearch, $pregExprReplace, $result);
                }
            }
        }

        $result = str_replace('  ', ' ', $result);
        $result = str_replace('  ', ' ', $result);

        return $result;
        //===
    }


    /**
     * Sanitizes given string for OrientDb
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeStringOrientDb ($string) {


        // remove quotation marks and placeholder (?)
        $search = array (
            '"',
            '\'',
            '?'
        );

        return trim(str_replace($search, '', $string));
        //===

        ///return self::sanitizeStringLucene ($string, TRUE);
        //===


    }



    /**
     * Sanitizes given string for Lucene
     *
     * @param string $string
     * @param boolean $strict If set to TRUE the filter will reduce the allowed signs to a minimum
     * @return string
     */
    public static function sanitizeStringLucene ($string, $strict = FALSE) {

        // Special chars
        /*
         * ^ = boosting - only allowed at the end of a word, must be followed by a number
         * ~ = fuzzy search - only allowed at the end of a word, can be followed by a number
         * ? = wildcard single - not allowed at the beginning of a word
         * * = wildcard multiple - not allowed at the beginning of a word
         * + = include - the word that follows has to be in the document - only at the beginning of words
         * - = exclude - the word that follows is not allowed in the document - only at the beginning of words
         * && = logical AND - not allowed at the beginning or the end of the string
         * AND = logical AND - not allowed at the beginning or the end of the string
         * || = logical OR - not allowed at the beginning or the end of the string
         * OR = logical OR - not allowed at the beginning or the end of the string
         * ( ) = grouping - for every opening parenthesis there should be a closing one
         * ! = not - only allowed at the beginning of words - useless without brackets - should be simply deleted here
         * \ = escape character - should be simply deleted here
         * {} = function unclear - should be simply deleted here
         * [] = function unclear - should be simply deleted here
         * $ = function unclear - should be simply deleted here
         * : = function unclear - should be simply deleted here
         * / = Slashes should be deleted here
         * ? = Used as placeholder in query - needs to be deleted here!
         */
        $search = array (
            '/(^|( ))(\^|\~|\?|\*)/',
            '/\^([^0-9]+)|\^$/',
            '/\~([^0-9 ]+)/',
            '/^(AND|OR|\&\&|\|\|)( )?|( )?(AND|OR|\&\&|\|\|)$/',
            '/\{|\}|\[|\]|\?|\$|\:|\\\|\!|\//',
            '/(\-|\+)+([ ]+)/',
            '/^([^\-\+]*)(\-|\+)+$/',
            '/(\-){2,}/',
            '/(\+){2,}/',
            '/[ ]{2,}/',
            '/\n/',
            '/\r/',
        );
        $replace = array (
            '$1',
            '$1',
            '$1',
            '',
            '',
            '$2',
            '$1',
            '-',
            '+',
            ' ',
            '',
            ''
        );

        if ($strict) {

            // no boosting, no fuzzy at all
            $search[] = '/\^([0-9](\.[0-9]+)?)?|\~([0-9](\.[0-9]+)?)?/';
            $replace[] = '';

            // no logical AND and OR at all
            $search[] = '/ (AND|OR|\&\&|\|\|) /';
            $replace[] = ' ';

            // no parenthesis, no quotation-marks
            $search[] = '/\(|\)|"|\'/';
            $replace[] = '';

            // no - or + at the beginning of a word
            $search[] = '/([ ]+)(\-|\+)+\b/';
            $replace[] = '$1';
            $search[] = '/^(\-|\+)+\b/';
            $replace[] = '';

        }


        // check parenthesis matches!
        // for each opening parenthesis there has to be a closing one!
        $specialKeyArray = array(
            0 => array (
                'start' => '(',
                'startRegEx' => '\(',
                'end' => ')',
                'endRegEx' => '\)'
            ),
        );

        foreach ($specialKeyArray as $key) {

            // check for parenthesis-match
            $deleteKey = TRUE;
            if (
                (strpos($string, $key['start']) !== FALSE)
                || (strpos($string, $key['end']) !== FALSE)
            ) {

                // here we check if for each opening parenthesis there is also a closing one
                $len = strlen($string);
                $stack = array();
                $deleteKeyForce = FALSE;
                for ($i = 0; $i < $len; $i++) {

                    switch ($string[$i]) {
                        case $key['start']:

                            array_push($stack, 0);
                            break;
                            //===

                        case $key['end']:

                            // if we have a closing one without an opening - we kill that beast
                            if (array_pop($stack) === NULL) {
                                $deleteKeyForce = TRUE;
                                break(2);
                            }

                            break;
                            //===

                        default:
                            break;
                            //===
                    }
                }

                $deleteKey = !empty($stack);
                if ($deleteKeyForce)
                    $deleteKey = $deleteKeyForce;

            }

            if ($deleteKey) {
                $search[] = '/' . $key['startRegEx'] . '|' . $key['endRegEx'] . '/';
                $replace[] = '';
            }
        }

        // check if we have an odd number of quotation marks - and remove them if so
        if ((substr_count($string, '"') % 2) > 0)
            $search[] = '/"/';
            $replace[] = '';

        if ((substr_count($string, '\'') % 2) > 0)
            $search[] = '/\'/';
            $replace[] = '';

        // replace double spaces
        $search[] = '/  /';
        $replace[] = ' ';

        return trim(preg_replace($search, $replace, $string));
        //===
    }


    /**
     * Merge content of array into string
     *
     * @param array $data Array with content to merge
     * @param string $separator Separator sign
     * @return string
     */
    public static function mergeToString($data, $separator = '. ') {

        // if separator is numeric, we use ascii-code
        if (is_numeric($separator))
            $separator = chr($separator);

        $content = '';
        if (is_array($data)) {

            // Merge content
            foreach ($data as $key => $value) {

                // for multi-dimensional arrays
                if (is_array($value)) {
                    foreach ($value as $subkey => $subvalue)
                        $content .= $value[$subkey] ? $value[$subkey] . $separator : '';

                // for normal arrays
                } else {
                    $content .= $data[$key] ? $data[$key] .  $separator : '';
                }
            }
        }

        // remove trailing line break and separator
        return self::stripTrailingLineBreaks($content, $separator);
        //===
    }


    /**
     * Strips all HTML-tags and entities
     *
     * @param string $data
     * @param string $separator Separator sign
     * @return string
     */
    public static function stripHtml($data, $separator = '. ') {


        if ($separator) {

            // remove existing line-breaks with separator, if the separator is not already there
            $data = preg_replace("/([" . trim($separator) . "]{" . strlen(trim($separator)) . "})?\n|\r/", $separator, $data);

            // strip HTML except for <br> and <br/>
            $data = html_entity_decode(strip_tags($data, '<br><br/>'));

            // now replace <br> and <br/> by the given separator
            $data = str_replace('<br />', $separator, str_replace('<br/>', $separator, str_replace('<br>', $separator, $data)));

            // strip double separators
            $data = self::stripDoubleSeparators($data, $separator);
        }

        // remove trailing line breaks
        return rtrim($data);
        //===
    }



    /**
     * Strips given signs and trims string
     *
     * @param string $content
     * @param string $additionalSigns Additional signs to remove
     * @return string
     */
    public static function stripTrailingLineBreaks($content, $additionalSigns = "") {

        return rtrim($content, " \t\n\r\0\x0B" . $additionalSigns);
        //===
    }

    /**
     * Strips double separators in string
     *
     * @param string $data
     * @param string $separator Separator sign
     * @return string
     */
    public static function stripDoubleSeparators ($data, $separator = '. ') {


        if ($separator) {

            // replace doubled separators by a single one
            // variation one
            $maxCount = 0;
            do {
                $data = str_replace($separator . $separator, $separator, $data);
                $maxCount++;
            } while (
                ($maxCount < 100)
                && (strpos($data, $separator . $separator) !== FALSE)
            );

            // variation two
            $maxCount = 0;
            do {
                $data = str_replace(trim($separator) . $separator, $separator, $data);
                $maxCount++;
            } while (
                ($maxCount < 100)
                && (strpos($data, trim($separator) . $separator) !== FALSE)
            );
        }

        // remove trailing line breaks
        return rtrim($data);
        //===
    }



    /**
     * Get TypoScript configuration
     *
     * @param string $key The item-key to return
     * @param integer $languageUid
     * @return mixed
     * @throws \RKW\RkwSearch\Exception
     */
    public static function getConfiguration($key = NULL, $languageUid = 0) {

        $settings = array();

        // load from TypoScript
        $settingsTemp = Common::getTyposcriptConfiguration();
        if ($settingsTemp['textHelper'])
            $settings = $settingsTemp['textHelper'];


        if (! $settings)
            throw new \RKW\RkwSearch\Exception('No valid configuration found.', 1422519937);
            //===

        if ($key) {

            if ($settings[$languageUid][$key])
                return $settings[$languageUid][$key];
                //===

            return $settings[0][$key];
            //===
        }

        return $settings;
        //===
    }


}