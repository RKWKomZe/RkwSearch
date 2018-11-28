<?php
namespace RKW\RkwSearch\Domain\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

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
 * Class QueueAnalysedKeywordsRepository
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueueAnalysedKeywordsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * Returns all elements ordered by crdate-field and status
     *
     * @param integer $limit
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @api
     */
    public function findByCrdateAndStatus($limit = 0) {

        $query = $this->createQuery();
        if ($limit != 0)
            $query->setLimit($limit);

        $query->matching($query->lessThan('status', 2));

        $query->setOrderings(array(
            'crdate' => QueryInterface::ORDER_ASCENDING,
            'status' => QueryInterface::ORDER_DESCENDING
        ));
        return $query->execute();
        //===
    }


    /**
     * Returns all elements that do not error status
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @api
     */
    public function findAllByStatus() {

        $query = $this->createQuery();
        $query->matching($query->lessThan('status', 2));

        return $query->execute();
        //===
    }
}