<?php

namespace RKW\RkwSearch\FuzzySearch;

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
 * Class Standard
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see http://pear.magdev.de/package/Text_ColognePhonetic
 */

class Standard implements EncoderInterface {


    /**
     * Encode a string
     *
     * @param string $string The string to encode
     * @return string
     */
    public static function encode($string) {

        // First pass: Strip non-alpha
        $string = preg_replace('/[^a-zöäüÖÄÜß]/i', '', $string);

        return soundex($string);
        //===
    }


}