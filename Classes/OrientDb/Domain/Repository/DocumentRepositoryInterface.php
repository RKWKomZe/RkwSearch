<?php
namespace RKW\RkwSearch\OrientDb\Domain\Repository;

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
 * Class DocumentRepositoryInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

interface DocumentRepositoryInterface extends RepositoryInterface {



    /**
     * Adds an object to this repository
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $object The object to add
     * @param boolean $forceInsert Forces insertion
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function add($object, $forceInsert = FALSE);
}
?>