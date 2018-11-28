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
 * Class QueueTaggedContentRepository
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class QueueTaggedContentRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {


    /**
     * Returns all elements ordered by crdate-field
     *
     * @param integer $limit
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @api
     */
    public function findByCrdateAndStatus($limit = 0) {

        $query = $this->createQuery();
        if ($limit != 0)
            $query->setLimit($limit);

        $query->matching($query->equals('status', 0));

        $query->setOrderings(array('crdate' => QueryInterface::ORDER_ASCENDING));
        return $query->execute();
        //===
    }


    /**
     * Really delete from DB!
     *
     * @overwrite
     * @param \RKW\RkwSearch\Domain\Model\QueueTaggedContent $object
     * @throws \RKW\RkwSearch\Exception
     */
    public function remove($object) {

        if (! $object instanceof $this->objectType)
            throw new \RKW\RkwSearch\Exception ('The modified object given to ' . __METHOD__ . ' was not of the type (' . $this->objectType. ') this repository manages.', 1417536760);
            //===

        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_rkwsearch_domain_model_queuetaggedcontent', 'uid = ' . $object->getUid());
    }
}