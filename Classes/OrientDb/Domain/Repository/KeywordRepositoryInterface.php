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
 * Interface KeywordBasesRepositoryInterface
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @uses \Doctrine\OrientDB
 * @see \TYPO3\CMS\Extbase\Persistence\OrientDb\Repository
 */

interface KeywordRepositoryInterface extends RepositoryInterface {

    /**
     * Deletes vertexes without edges
     *
     * @param string $edgeClass The edge class to check for
     * @param integer $limit
     * @return boolean
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function cleanup($edgeClass, $limit);
}
?>