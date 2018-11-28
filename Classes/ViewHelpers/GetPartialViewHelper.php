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
 * Class GetPartialViewHelper
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 3 or later
 */
class GetPartialViewHelper extends \RKW\RkwBasics\ViewHelpers\GetPartialViewHelper {

    /**
     * @param string $type
     * @param string $subPath
     *
     * @return array
     */
    public function render($type, $subPath = '') {

        $type = str_replace('Document', '', $type);
        return parent::render($type, $subPath);
        //===

    }


}