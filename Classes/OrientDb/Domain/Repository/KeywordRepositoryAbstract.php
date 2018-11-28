<?php
namespace RKW\RkwSearch\OrientDb\Domain\Repository;
use \RKW\RkwSearch\OrientDb\Storage\Query\Query;
use \RKW\RkwSearch\OrientDb\Helper\Common;

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
 * Class KeywordRepositoryAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

class KeywordRepositoryAbstract extends RepositoryAbstract {


    /**
     * Deletes vertexes without edges
     *
     * @param string $edgeClass The edge class to check for
     * @param integer $limit
     * @return integer
     * @throws \RKW\RkwSearch\Exception
     * @api
     */
    public function cleanup($edgeClass, $limit = 500) {

        // check if edge-model exists!
        if (
            (! $edgeModelName = Common::getOrientModelFromClassName($edgeClass))
            || (! class_exists($edgeModelName))
            || (! $edgeModel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Common::getOrientModelFromClassName($edgeClass)))
            || (! $edgeModel instanceof \RKW\RkwSearch\OrientDb\Domain\Model\EdgeInterface)
        )
            throw new \RKW\RkwSearch\Exception (sprintf('The model "%s" for edge-class "%s" does not exist or does not implement \RKW\RkwSearch\OrientDb\Domain\Model\EdgeInterface.', $edgeModelName, $edgeClass), 1422971202);
            //===

        /* OLD VERSION - DANGER: In new Version there is no outE, and thus ALL data would be deleted!
            $query = new Query();
            $query->select(array('*'));
            $query->from(array($this->getOrientDbClass()), FALSE);
            $query->resetWhere();
            $query->andWhere('outE(?).size() = 0 AND inE(?).size() = 0', array($edgeClass, $edgeClass));
            $query->andWhere('debug = ?', intval($this->debugMode));
            $query->limit($limit);
        */

        // New Query
        // workaround for bug @see: https://github.com/orientechnologies/orientdb/issues/3594
        //
        // select * FROM (SELECT outE("EdgeContains").size() AS EdgeSizeOut,
        // inE("EdgeContains").size() AS EdgeSizeIn, @class, @rid, @type FROM
        // KeywordVariations WHERE debug = 0 LIMIT 500) WHERE EdgeSizeIn = 0
        $query = new Query();
        $query->select(array('*'));

        $subQuery = new Query ();
        $subQuery->select(array('*, outE("' . addslashes($edgeClass) . '").size() AS EdgeSizeOut, inE(' . addslashes($edgeClass) . ').size() AS EdgeSizeIn'));
        $subQuery->from(array($this->getOrientDbClass()), FALSE);
        $subQuery->andWhere('debug = ?', intval($this->debugMode));
        $subQuery->limit($limit);

        $query->fromQuery($subQuery);
        $query->where('EdgeSizeIn = ?', 0);

        // get results
        if ($result = $this->getOrientDbDatabase()->execute($query)) {

            if (count($result)) {

                // delete vertexes
                /** @var \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface $entry */
                $counter = 0;
                foreach ($result as $entry)
                    if ($this->remove($entry))
                        $counter++;

                $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Deleted %s unrelated vertexes of class %s.', count($result), $this->getOrientDbClass()));
                return $counter;
                //===
            }

        }

        $this->getLogger()->log(\TYPO3\CMS\Core\Log\LogLevel::INFO, sprintf('Nothing to clean up in class %s.', $this->getOrientDbClass()));
        return 0;
        //===



    }

}
?>