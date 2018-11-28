<?php
namespace RKW\RkwSearch\OrientDb\Domain\Model;

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
 * Class DocumentInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */
interface DocumentInterface extends ModelInterface {


    /**
     * Get the top keywords
     *
     * @return array
     */
    public function getTopKeywords();


    /**
     * set the top keywords
     *
     * @param array $value
     * @return void
     */
    public function setTopKeywords($value);



}