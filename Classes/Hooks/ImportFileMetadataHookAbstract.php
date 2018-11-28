<?php
namespace RKW\RkwSearch\Hooks;
use RKW\RkwSearch\OrientDb\Helper\Common;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
 * Class ImportFileMetadataHookAbstract
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */


abstract class ImportFileMetadataHookAbstract extends ImportHookAbstract implements ImportFileMetadataHookInterface {

    /**
     * @var string Source-table of data
     */
    protected $table = 'sys_file_metadata';

    /**
     * Imports data from file_metadata and sends backend messages accordingly
     *
     * @param mixed $data
     * @return void
     */
    public function import($data = NULL) {

        $importedData = array ();
        try {
            try {
                try {

                    // check if there is any data to import
                    if (empty($data))
                        throw new \RKW\RkwSearch\Exception('No valid data given.', 1430916179);
                        //===

                    // if we get an array we are to add a new object
                    $repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Common::getOrientRepositoryFromTableName('sys_file_metadata'));
                    if (is_array($data)) {

                        // load array into model
                        $model = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(Common::getOrientModelFromTableName($this->table), $data);;

                        // save data to repository
                        $repository->add($model);

                        $importedData[] = $data;

                    // if we get an id only, we are to delete an object
                    } else if (intval($data > 0)) {

                        // load model by uid from database
                        $result = $repository->findByUid($data);

                        // save data to repository
                        if ($result instanceOf \RKW\RkwSearch\OrientDb\Collection\Document) {
                            foreach($result as $model)
                                $repository->remove($model);
                        } else {
                            $repository->remove($result);
                        }
                    }

                    // @toDo: Don't send a message here since it may be more than one file if an folder is opened for the first time in BE
                    // $this->setBackendMessage('backend.message.header', 'backend.message.successfully_saved_1399294437', 'OK');

                } catch (\RKW\RkwSearch\StorageException $e) {
                    $this->setBackendMessage('backend.error.header', 'backend.error.fatal_error_1399293930', 'ERROR', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
                }

            } catch (\RKW\RkwSearch\Exception $e) {
                $this->setBackendMessage('backend.warning.header', 'backend.warning.little_problem_1399294227', 'WARNING', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
            }

        } catch (\Exception $e) {
            $this->setBackendMessage('backend.error.header', 'backend.error.fatal_error_1399293930', 'ERROR', array($e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine()));
        }

        $this->debug('import', $importedData);

    }

}
?>