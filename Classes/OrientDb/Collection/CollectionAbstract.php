<?php
namespace RKW\RkwSearch\OrientDb\Collection;
use RKW\RkwSearch\OrientDb\Helper\Common;

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
 * Class CollectionAbstract
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see \Iterator, \Serializable, \Countable
 */

abstract class CollectionAbstract extends \RKW\RkwSearch\Collection\CollectionAbstract {

    /**
     * Record type Document
     * @var string
     */
    const RECORD_TYPE_DOCUMENT = 'd';


    /**
     * Get element by position
     *
     * @param integer $position Position of array to return
     * @return object | array
     */
    public function getElement ($position) {


        if (($this->data[$this->keys[$position]])) {

            // check if class is given
            if (
                (is_array($this->data[$this->keys[$position]]))
                && ($className = $this->data[$this->keys[$position]]['@class'])
                && ($this->data[$this->keys[$position]]['@type'] == self::RECORD_TYPE_DOCUMENT)
            ){

                // try to load model and return it
                $modelName = Common::getOrientModelFromClassName($className);
                if (class_exists($modelName))
                    return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($modelName, $this->data[$this->keys[$position]]);
                    //===

            } else if (
                ($this->data[$this->keys[$position]] instanceof \OrientDBRecord)
                && ($this->data[$this->keys[$position]]->type == self::RECORD_TYPE_DOCUMENT)
            ) {

                $record = $this->data[$this->keys[$position]];

                // fix 1: if record contains more than one edge, the returned edge-data results in an
                // endless loop in OrientDBRecordDecoder
                // therefore we replace the problematic content before decoding
                $content = preg_replace ( '/(\%[^;"]+;)/' , '0' , $record->content);
                $parser = new \OrientDBRecordDecoder(rtrim($content));

                // try to load model and return it
                $className = $parser->className;
                if (
                    (! $className)
                    && ($parser->data)
                    && ($parser->data->class)
                )
                    $className = $parser->data->class;

                $modelName = Common::getOrientModelFromClassName($className);

                if (class_exists($modelName))
                    return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                        $modelName,
                        array_merge(
                            get_object_vars($parser->data),
                            array (
                                '@rid' => (($parser->data) && ($parser->data->rid) && ($parser->data->rid->clusterID) && ($parser->data->rid->recordPos)) ? '#' . $parser->data->rid->clusterID . ':' . $parser->data->rid->recordPos : '#' . $record->recordID,
                                '@type' => $record->type,
                                '@class' => $className
                            )
                        )
                    );
                    //===
            }
        }

        return parent::getElement($position);
        //===
    }

}