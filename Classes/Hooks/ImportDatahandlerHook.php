<?php
namespace RKW\RkwSearch\Hooks;

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
 * Class ImportDatahandlerHook
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class ImportDatahandlerHook extends ImportDatahandlerHookAbstract {


    /**
     * Hook: processDatamap_afterDatabaseOperations - fired on new and update operations
     *
     * @param string $status Status of the current operation, 'new' or 'update
     * @param string $table The table currently processing data for
     * @param string $id The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param array $fieldArray The record that has been updated
     * @param object $object \TYPO3\CMS\Core\DataHandling\DataHandler
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::processRemapStack
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $object) {

        $this->init($status, $table, $id, $object);
        $this->import();
    }



    /**
     * Hook: processCmdmap_deleteAction - fired when datasets are deleted
     *
     * @param string $table The table currently processing data for
     * @param string $id The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param array $recordToDelete The record that has been deleted including all data
     * @param boolean $recordWasDeleted Shows if record was already deleted (e.g. by another hook-call)
     * @param object $object \TYPO3\CMS\Core\DataHandling\DataHandler
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::deleteAction
     */
    public function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, $object) {

        $this->init('delete', $table, $id, $object);
        $this->import();

    }


    /**
     * Hook: processCmdmap_postProcess- fired when $command has been executed on record
     *
     * @param string $command The command that has been executed. Possible values are:
     * @param string $table The table currently processing data for
     * @param string $id The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param mixed $value Array which contains information to current command
     * @param object $object \TYPO3\CMS\Core\DataHandling\DataHandler
     * @param array $pasteUpdate Data that has been updated or inserted
     * @param array $pasteDatamap DONT KNOW
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::process_cmdmap
     */
    public function processCmdmap_postProcess($command, $table, $id, $value, $object, $pasteUpdate, $pasteDatamap) {

        if (
            (is_array($value))
            && ($value['action'])
            && ($value['action'] == 'swap')
            && ($value['swapWith'])
            && ($value['swapWith'] > 0)
        ) {
            $this->init('swap', $table, $id, $object);
            $this->import();
        }

    }

    /**
     * Hook: moveRecord_firstElementPostProcess - when record is moved as first record into a page
     *
     * @param string $table The table currently processing data for
     * @param string $id The record uid currently processing data for, [integer]
     * @param integer $destPid The target pid
     * @param array $moveRec The properties of the record after being moved
     * @param array $updateFields The fields that were updated
     * @param object $object \TYPO3\CMS\Core\DataHandling\DataHandler
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::moveRecord_raw
     */
    public function moveRecord_firstElementPostProcess( $table, $id, $destPid, $moveRec, $updateFields, $object) {

        $this->init('move', $table, $id, $object, $moveRec['pid']);
        $this->import();

    }

    /**
     * Hook: moveRecord_afterAnotherElementPostProcess - when record is moved into a page where there already are records
     *
     * @param string $table The table currently processing data for
     * @param string $id The record uid currently processing data for, [integer]
     * @param integer $destPid The target pid (resolved via sort-array)
     * @param integer $origDestPid The original target pid
     * @param array $moveRec The properties of the record after being moved
     * @param array $updateFields The fields that were updated
     * @param object $object \TYPO3\CMS\Core\DataHandling\DataHandler
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::moveRecord_raw
     */
    public function moveRecord_afterAnotherElementPostProcess( $table, $id, $destPid, $origDestPid, $moveRec, $updateFields, $object) {

        $this->init('move', $table, $id, $object, $moveRec['pid']);
        $this->import();

    }


}
?>