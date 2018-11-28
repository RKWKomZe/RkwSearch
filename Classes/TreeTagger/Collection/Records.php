<?php
namespace RKW\RkwSearch\TreeTagger\Collection;

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
 * Class Records
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_TreeTagger
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 * @see \Iterator, \Serializable, \Countable
 */

class Records extends \RKW\RkwSearch\Collection\CollectionAbstract {

    /**
     * Forwards to given position in array - but only if there is something to forward
     *
     * @param integer $i Position to jump to
     * @return boolean
     */
    public function forwardToPosition ($i) {

        // only set position if we step forward with it
        if ($i > $this->position)
            $this->position = $i;

        // we return true in the case of no change, too
        if ($i >= $this->position)
            return TRUE;
            //===

        return FALSE;
        //===

    }


    /**
     * Get element by position
     *
     * @param integer $position Position of array to return
     * @return object | array
     */
    public function getElement ($position) {

        if ($this->data[$position]) {

            if (class_exists('RKW\\RkwSearch\\TreeTagger\\TreeTaggerRecord'))
                if (! $this->data[$position] instanceof \RKW\RkwSearch\TreeTagger\TreeTaggerRecord)
                    return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('RKW\\RkwSearch\\TreeTagger\\TreeTaggerRecord', $this->data[$position]);
                    //===
        }

        return parent::getElement($position);
        //===
    }

}