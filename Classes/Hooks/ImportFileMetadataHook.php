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
 * Class ImportFileMetadataHook
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class ImportFileMetadataHook extends ImportFileMetadataHookAbstract {


    /**
     * SignalSlot: recordCreated - called when record on table sys_file_metadata is created
     *
     * @param array $data The record that has been created
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::processRemapStack
     */
    public function recordCreatedSlot ($data) {

        $this->import($data);

    }

    /**
     * SignalSlot: recordDeleted - called when record on table sys_file_metadata is deleted
     *
     * @param array $uid The uid of the record that has been deleted
     * @return void
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::processRemapStack
     */
    public function recordDeletedSlot ($uid) {

        $this->import($uid);

    }

}
?>