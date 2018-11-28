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
 * Class DocumentPages
 *
 * @package RKW_RkwSearch
 * @subpackage  TYPO3_RkwSearch_OrientDb
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm
 */

class DocumentPages extends DocumentAbstract {


    /**
     * @var int Doktype
     */
    protected $doktype;

    /**
     * @var string Title of page
     */
    protected $title;

    /**
     * @var string Subtitle of page
     */
    protected $subtitle;

    /**
     * @var string Abstract of page
     */
    protected $abstract;

    /**
     * @var string Description of page
     */
    protected $description;

    /**
     * @var string Keywords of page
     */
    protected $keywords;

    /**
     * @var string Contents of page
     */
    protected $content;

    /**
     * @var integer If we have PDF-imports we use this
     */
    protected $pdfImport;

    /**
     * @var string If we have PDF-imports in a subpage we use this
     */
    protected $pdfImportSub;

    /**
     * @var integer Contains the parent uid of a pdf-import subpage
     */
    protected $pdfImportParentUid;

    /**
     * @var string CSS-class of object
     */
    protected $cssClass;

    /**
     * @var string documentType of object
     */
    protected $documentType;

    /**
     * @var string boxTemplateName of object
     */
    protected $boxTemplateName;



    /**
     * Returns the preview image
     *
     * @return string
     */
    public function getPreviewImage() {

        /** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');

        // get uid from parent import page for the image
        $uid = $this->getUid();
        if (
            ($this->getPdfImportSub())
            && ($this->getPdfImportParentUid())
        )
            $uid = $this->getPdfImportParentUid();

        $fileObjects = $fileRepository->findByRelation(Common::getTypo3TableFromOrientClass($this), 'txRkwbasicsTeaserImage', $uid);

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

    /**
     * Returns the preview image
     *
     * @return string
     */
    public function getCoverImage() {

        /** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');

        // get uid from parent import page for the image
        $uid = intval($this->getUid());
        if (
            ($this->getPdfImportSub())
            && ($this->getPdfImportParentUid())
        )
           $uid = intval($this->getPdfImportParentUid());

        $fileObjects = $fileRepository->findByRelation(Common::getTypo3TableFromOrientClass($this), 'txRkwbasicsFile', $uid);

        // check for override with explicit cover - files!
        $fileObjectsCover = $fileRepository->findByRelation(Common::getTypo3TableFromOrientClass($this), 'txRkwbasicsCover', $uid);
        if (count($fileObjectsCover) > 0)
            $fileObjects = $fileObjectsCover;


        /** @var \TYPO3\CMS\Core\Resource\FileReference $value*/
        foreach ($fileObjects as $key => $value) {

            $ref = $value->getOriginalFile()->getProperties();

            if ($ref['identifier'])
                return 'fileadmin' . $ref['identifier'];
                //===
        }


        return NULL;
        //===
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitleParent() {


        // override attribute by parent data
        if (intval($this->getPdfImportParentUid())){

            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $pagesRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\PagesRepository');

            /** @var \RKW\RkwSearch\Domain\Model\Pages $page */
            if ($page = $pagesRepository->findByUid(intval($this->getPdfImportParentUid())))
                return $page->getTitle();
                //===
        }

        return $this->getProperty('title');
        //===
    }


    /**
     * Returns the subtitle
     *
     * @return string
     */
    public function getSubtitleParent() {

        // override attribute by parent data
        if (intval($this->getPdfImportParentUid())){

            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $pagesRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\PagesRepository');

            /** @var \RKW\RkwSearch\Domain\Model\Pages $page */
            if ($page = $pagesRepository->findByUid(intval($this->getPdfImportParentUid())))
                return $page->getSubtitle();
                //===
        }

        return $this->getProperty('subtitle');
        //===
    }


    /**
     * Returns the uid
     *
     * @return string
     */
    public function getUidParent() {

        // override attribute by parent data
        if (intval($this->getPdfImportParentUid()))
            return $this->getPdfImportParentUid();
            //===

        return $this->getProperty('uid');
        //===
    }

    /**
     * Returns the abstract
     *
     * @return string
     */
    public function getAbstract() {

        // get uid from parent import page for the abstract
        if ($this->getPdfImportParentUid()) {

            $objectManager =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
            $pagesRepository = $objectManager->get('RKW\RkwSearch\Domain\Repository\PagesRepository');

            /** @var \RKW\RkwSearch\Domain\Model\Pages $page */
            if ($page = $pagesRepository->findByUid(intval($this->getPdfImportParentUid())))
                return $page->getTxRkwbasicsTeaserText();
                //===
        }

        return $this->getProperty('abstract');
        //===
    }


    /**
     * Checks if item has import parent
     *
     * @return boolean
     */
    public function getHasImportParent() {

        // get uid from parent import page for the abstract
        if (
            ($this->getPdfImportParentUid())
            && ($this->getPdfImportParentUid() != $this->getUid())
        )
            return TRUE;
            //===

        return FALSE;
        //===
    }
}
