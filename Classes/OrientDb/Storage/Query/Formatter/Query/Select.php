<?php
namespace RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query;

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
 * Class Query
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 */

class Select extends \RKW\RkwSearch\OrientDb\Storage\Query\Formatter\Query implements \Doctrine\OrientDB\Query\Formatter\Query\TokenInterface {

    /**
     * Formats given values
     *
     * @param array $values
     * @return string
     */
    public static function format(array $values) {

        return self::implodeRegular($values, '[:punct:]|\s');
        //===
    }
}
