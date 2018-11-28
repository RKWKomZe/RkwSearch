<?php

namespace RKW\RkwSearch\FuzzySearch;
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
 * Class ColognePhonetic
 *
 * @package RKW_RkwSearch
 * @author Marco Graetsch <info@magdev.de>, Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright 2008 magdev, Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see http://pear.magdev.de/package/Text_ColognePhonetic
 */

class ColognePhonetic implements EncoderInterface {

    /**
     * Store the rules for conversion
     * @var array
     */
    protected static $rules = array(
        array('/[aejiouyäöüÄÖÜ]/i', '0'),
        array('/[b]/i', '1'),
        array('/[p][^h]?/i', '1'),
        array('/[dt]([csz])/i', '8$1'),
        array('/[dt](?<=[^csz])/i', '2$1'),
        array('/[fvw]/i', '3'),
        array('/p([h])/i', '3$1'),
        array('/([^sz])c([ahkloqrux])/i', '${1}4$2'),
        array('/[gkq]/i', '4'),
        array('/([^sz])c([ahkoqux])/i', '${1}4$2'),
        array('/([^ckq]??)x/i', '${1}48'),
        array('/[l]/i', '5'),
        array('/[mn]/i', '6'),
        array('/[r]/i', '7'),
        array('/([sz])c/i', '${1}8'),
        array('/[szß]/i', '8'),
        array('/^c([^ahkloqrux]??)/i', '8$1'),
        array('/([ckq])x/i', '${1}8'),
        array('/[h]/i', ''),
    );

    /**
     * Encode a string
     *
     * @param string $inputString The string to encode
     * @param string $delimiter Delimiter
     * @return string
     */
    public static function encode($inputString, $delimiter = ' ') {

        $final = array ();
        foreach (explode($delimiter, $inputString) as $string) {

            // get raw string with encoded german umlauts for first letter
            // we need to replace the german umlauts here because those are multi-byte values
            $stringRaw = Text::encodeGermanUmlauts($string);

            // First pass: Strip non-alpha
            $string = preg_replace('/[^a-zöäüÖÄÜß]/i', '', $string);

            // Second pass: Apply conversion-rules
            $string = self::applyRules($string);

            // Third pass: Strip double occurrences
            $string = self::stripDoubles($string);

            if (strlen($string < 1))
                continue;
                //===

            // get first letter - must be an alpha-value!
            $cnt = 0;
            $firstLetter = '';
            do {

                if (preg_match('/[a-z]/i', $stringRaw[$cnt])) {
                    $firstLetter = $stringRaw[$cnt];
                    break;
                    //===
                }
                $cnt++;

            } while ($cnt < strlen($string));

            // Fourth pass: Strip all zeros except a leading one
            // we also include the first letter (this is our own solution. Otherwise "fach" and "weg" are both coded with 34)
            $final[] = trim(strtolower($firstLetter . self::stripZeros($string)));
        }

        return implode($delimiter, $final);
        //===
    }


    /**
     * Apply the ruleset for conversion
     *
     * @param string $string
     * @return string
     */
    protected function applyRules($string) {
        foreach (self::$rules as $rule) {
            $string = preg_replace($rule[0], $rule[1], $string);
        }
        return $string;
        //===
    }

    /**
     * Strip double occurrences of numbers
     *
     * @param string $string
     * @return string
     */
    protected function stripDoubles($string) {

        $i = 0;
        while ($i <= 9) {
            $string = preg_replace('/['.$i.']{2,}/', $i, $string);
            $i++;
        }
        return $string;
        //===
    }


    /**
     * Strip all zeros except a leading one
     *
     * @param string $string
     * @return string
     */
    protected function stripZeros($string) {
        $first = '';

        if ($string[0] === '0') {
            $first = '0';
            $string = substr($string, 1);
        }

        return $first.str_replace('0', '', $string);
        //===
    }



}