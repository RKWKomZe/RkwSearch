<?php
namespace RKW\RkwSearch\OrientDb\Domain\Model;
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
 * Class DocumentAuthors
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

class DocumentAuthors extends DocumentAbstract {


    /**
     * @var string
     */
    protected $firstname;


    /**
     * @var string
     */
    protected $lastname;

    /**
     * @var string
     */
    protected $middlename;

    /**
     * @var string
     */
    protected $titleBefore;

    /**
     * @var string
     */
    protected $titleAfter;

    /**
     * @var integer
     */
    protected $internal;


    /**
     * Returns the image
     *
     * @return string
     */
    public function getImage() {

        /** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');

        // get uid
        $uid = $this->getUid();
        $fileObjects = $fileRepository->findByRelation(Common::getTypo3TableFromOrientClass($this), 'image_boxes', $uid);

        /** @var \TYPO3\CMS\Core\Resource\FileReference $value*/
        foreach ($fileObjects as $key => $value) {

            // return reference id here for enabled cropping
            if ($value->getUid())
                return $value->getUid();
                //===

        }

        return NULL;
        //===
    }


}
