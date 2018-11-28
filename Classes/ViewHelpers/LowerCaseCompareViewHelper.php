<?php
namespace RKW\RkwSearch\ViewHelpers;

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
 * Class LowerCaseCompareViewHelper
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 3 or later
 */
class LowerCaseCompareViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Compares to string ind lowercase
     *
     * @param string $string1
     * @param string $string2
     * @return boolean
     */
    public function render ($string1 = '', $string2 = '') {

        return (strtolower($string1) == strtolower($string2));
        //===
    }
}