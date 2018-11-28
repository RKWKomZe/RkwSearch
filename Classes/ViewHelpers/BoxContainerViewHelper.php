<?php
namespace RKW\RkwSearch\ViewHelpers;

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
 * Class BoxContainerViewHelper
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 3 or later
 */
class BoxContainerViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @var string Defines the type of boxes in order to load the correspondend data rom config
     */
    protected $type = 'default';


    /**
     * @var array Box-types that are NEVER shown as smaller ones
     */
    protected $nonSmallItemTypes = array ();


    /**
     * @var \RKW\RkwSearch\OrientDb\Collection\Document Contains the result list of query
     */
    protected $dataArray = array ();


    /**
     * @var array Settings
     */
    protected $settings = array ();
    
    /**
     * @param \RKW\RkwSearch\OrientDb\Collection\Document $results
     * @param integer $maxResults Maximum number of results
     * @param string $type Sets the box type for the config
     * @param boolean $hideMore If set to TRUE no more-link will be generated
     * @param array $specialResults Array with special results for special boxes in results
     *
     * @return array
     */
    public function render(\RKW\RkwSearch\OrientDb\Collection\Document $results,  $maxResults, $type = NULL, $hideMore = FALSE, $specialResults = array()) {

        if ($type)
            $this->type = $type;

        if (count($results) > 0) {

            // save results in object
            $this->dataArray = $results;

            // set defaults
            $numberBigBoxes = 5;
            $numberSmallBoxes = 3;

            /** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager */
            $configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
            if ($configurationManager) {

                $settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
                $this->settings = $settings['settings'];
                if (isset($this->settings['search']['boxes'][$this->type]['numberBigBoxes']))
                    $numberBigBoxes = intval($this->settings['search']['boxes'][$this->type]['numberBigBoxes']);

                if (isset($this->settings['search']['boxes'][$this->type]['numberSmallBoxes']))
                    $numberSmallBoxes = intval($this->settings['search']['boxes'][$this->type]['numberSmallBoxes']);

                if (isset($this->settings['search']['boxes'][$this->type]['displayTemplateAlwaysAsBigBox']))
                    $this->nonSmallItemTypes = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['search']['boxes'][$this->type]['displayTemplateAlwaysAsBigBox'], TRUE);
            }

            //================================================
            // calculate number of full loops and boxes left
            // if we have some special boxes we have to consider that in calculation!
            $specialBoxesSubstract = 0;
            if (count($specialResults) > 0) {
                $specialBoxesSubstract = 1;
                $numberSmallBoxes -= $specialBoxesSubstract;
            }
            $fullLoops = floor((count($results)-1) / ($numberBigBoxes + $numberSmallBoxes));
            $restBoxes = (count($results)-1) % ($numberBigBoxes + $numberSmallBoxes);
            if (
                (count($this->dataArray) <= $maxResults)
            ) {
                $fullLoops = floor((count($results)) / ($numberBigBoxes + $numberSmallBoxes));
                $restBoxes = count($results) % ($numberBigBoxes + $numberSmallBoxes);
            }
            // at the end we definitely need at least 3 boxes
            // if we don't have them, we have to reduce the full loops
            // BUT only, if we have some results more - otherwise we won't need a more-link
            // AND: We need 3 boxes here too - because the last one gets ignored
            // it only is the indicator that there is some more!
            if (
                ($fullLoops > 0)
                && (
                    ($restBoxes < (2 + $specialBoxesSubstract))
                    && (count($this->dataArray) > $maxResults)
                )
            ) {
                $fullLoops--;
                $restBoxes += ($numberBigBoxes + $numberSmallBoxes);
            }

            // calculate number of complete boxes
            $completeBoxes = $fullLoops * ($numberBigBoxes + $numberSmallBoxes);
            $restBigBoxes = $restBoxes;

            if ($restBigBoxes >= 2)
                $restBigBoxes = $restBoxes - 2;

            //================================================
            // now build boxes
            $bigBoxCounter = 0;
            $bigBoxRestCounter = 0;
            $smallBoxCounter = 0;
            $smallBoxRestCounter = 0;
            $resultArray = array();
            $counter = 0;
            foreach ($results as $key => $item) {

                if ($item instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface) {

                    // normal treatment when we have enough boxes!
                    //===============================================
                    if ($counter < $completeBoxes) {

                        // big boxes
                        if (
                            ($bigBoxCounter < $numberBigBoxes)
                            || ($numberSmallBoxes < 1)
                        ) {

                            $this->bigBoxHandler($item, $resultArray, $bigBoxCounter, $numberBigBoxes);

                        // small boxes
                        } else if ($smallBoxCounter < $numberSmallBoxes) {

                            // check if there are elements that have to be displayed as big boxes
                            /*if (! $this->isPermanentBigBox (($counter - $smallBoxCounter),($counter - $smallBoxCounter) + $numberSmallBoxes -1)) {
                                $this->smallBoxHandler($item, $specialResults, $resultArray, $smallBoxCounter);
                            } else {
                                $this->bigBoxHandler($item, $resultArray, $smallBoxCounter, $numberSmallBoxes);
                            }
                            */
                            $this->bigBoxHandler($item, $resultArray, $smallBoxCounter, $numberSmallBoxes);

                            // reset
                            if ($smallBoxCounter >= $numberSmallBoxes) {
                                $bigBoxCounter = 0;
                                $smallBoxCounter = 0;
                            }

                        // fallback if numberSmallBoxes < 1 - still needed???
                       /* } else {
                            $bigBoxCounter = 0;
                            $smallBoxCounter = 0;

                       */
                        }

                        // Treatment for the rest of the boxes
                        //===============================================
                    } else {

                        // big boxes first
                        if ($bigBoxRestCounter < $restBigBoxes) {

                            $this->bigBoxHandler($item, $resultArray, $bigBoxRestCounter, $restBigBoxes);

                        // small boxes
                        } else {

                            $this->smallBoxHandler($item, $specialResults, $resultArray, $smallBoxRestCounter, $maxResults, (! $hideMore));

                            if (! $hideMore)
                                if ($smallBoxRestCounter > 1)
                                    break;
                                    //===
                        }
                    }

                    $counter++;
                }
            }

            return $resultArray;
            //===
        }

        return array();
        //===
    }


    /**
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface $item Contains the current item to process
     * @param array &$resultArray The final data
     * @param integer &$counter Counter for iteration
     * @param integer $totalBoxes Maximum number of boxes
     *
     * @return array
     */
    protected function bigBoxHandler(\RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface $item, &$resultArray, &$counter, $totalBoxes) {

        $class = 'big';
        $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['big']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['big']) : 60;
        $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['big']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['big']) : 230;
        $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['big']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['big']) : 20;
        $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['big']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['big']) : 35;

        $wrapOpen = TRUE;
        $wrapClose = TRUE;
        $boxType = 1;

        // build "packages" of boxes
        $packageNumber = 4;
        if ($totalBoxes % 2 != 0)
            $packageNumber = 5;

        // calculate how many packages match into the maximum number of boxes
        // and calculate what is the highest valid value for the counter based on that
        $maxPackages = floor($totalBoxes / $packageNumber);
        $maxPackageCounter = 0;
        if ($counter > 0)
            $maxPackageCounter = $packageNumber * $maxPackages -1;

        // check if the number of available items is big enough
        if ($counter <= $maxPackageCounter ){

            // check starting point - differs for odd numbers
            $startingPointAdd = 0;
            if ($packageNumber % 2 != 0)
                $startingPointAdd = 1;


            // set box values
            if ($counter > 0) {

                // get keys of boxes
                $startingBoxNumber = (($packageNumber * (floor( $counter / $packageNumber))) + $startingPointAdd + 1);
                $endBoxNumber = (($packageNumber * (floor( $counter / $packageNumber))) + $startingPointAdd + 2) ;

                // include some half-split boxes and check if the box-types are not from the forbidden garden
                /*if (! $this->isPermanentBigBox ($startingBoxNumber, $endBoxNumber)) {
                    if ($counter == $startingBoxNumber) {
                        $class = '';
                        $boxType = 2;
                        $wrapOpen = TRUE;
                        $wrapClose = FALSE;
                        $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) : 70;
                        $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) : 0;
                        $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) : 20;
                    }
                    if ($counter == $endBoxNumber) {
                        $class = '';
                        $boxType = 2;
                        $wrapOpen = FALSE;
                        $wrapClose = TRUE;
                        $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) : 70;
                        $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) : 0;
                        $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) : 20;
                    }
                }*/
            }
        }

        $resultArray[] = array (
            'item' => $item,
            'boxType' => $boxType,
            'cssClassSize' => $class,
            'cssClass' => ($item->getCssClass() ? ' ' . $item->getCssClass() : ' topic-1'),
            'wrapOpen' => $wrapOpen,
            'wrapClose' => $wrapClose, 
            'headerCrop' => $headerCrop,
            'contentCrop' => $contentCrop,
            'footerCrop' => $footerCrop,
            'flagCrop' => $flagCrop
        );
        $counter++;

    }


    /**
     * Handling of small boxes
     *
     * @param \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface $item Contains the current item to process
     * @param array &$specialItemsArray The array with special link objects
     * @param array &$resultArray The final data
     * @param integer &$counter Counter for iteration
     * @param integer $maxResults Maximum number of results displayed
     * @param boolean $isMoreLink If the function is called in more-link context
     *
     * @return array
     */
    protected function smallBoxHandler(\RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface $item, &$specialItemsArray, &$resultArray, &$counter, $maxResults = 0, $isMoreLink = FALSE) {

        // first one is half
        $boxType = 2;
        $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) : 70;
        $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) : 0;
        $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) : 20;
        $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['half']) : 35;

        if ($counter == 0) {

            $resultArray[] = array (
                'item' => $item,
                'boxType' => $boxType,
                'cssClassSize' => '',
                'cssClass' => ($item->getCssClass() ? ' ' . $item->getCssClass() : ' topic-1'),
                'wrapOpen' => TRUE,
                'wrapClose' => FALSE,
                'headerCrop' => $headerCrop,
                'contentCrop' => $contentCrop,
                'footerCrop' => $footerCrop,
                'flagCrop' => $flagCrop
            );
            $counter++;


        // the second (and third) is small
        } else  {

            $class = 'small';
            $closeWrap = FALSE;
            $boxType = 3;
            $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) : 40;
            $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) : 0;
            $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) : 8;
            $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) : 10;

            // if there is no special stuff, we keep the boxes small
            // and need a closing tag for the last
            if (
                (count($specialItemsArray) < 1)
                && (! $isMoreLink)
                && ($counter == 2)
            ){

                $closeWrap = TRUE;
            }

            // if there is no data for the more link, we need a closing tag here
            //@toDo: check!
            if (
                (
                    ($isMoreLink)
                    && (count($this->dataArray) <= $maxResults)
                )
            ){
                $class = '';
                $closeWrap = TRUE;
                $boxType = 2;
                $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['half']) : 70;
                $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['half']) : 0;
                $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['half']) : 20;
                $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['half']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['half']) : 35;

            }

            $resultArray[] = array(
                'item' => $item,
                'boxType' => $boxType,
                'cssClassSize' => $class,
                'cssClass' => ($item->getCssClass() ? ' ' . $item->getCssClass() : ' topic-1'),
                'wrapOpen' => FALSE,
                'wrapClose' => $closeWrap,
                'headerCrop' => $headerCrop,
                'contentCrop' => $contentCrop,
                'footerCrop' => $footerCrop,
                'flagCrop' => $flagCrop
            );
            $counter++;


            // the third is special - if available
            // if we are in more-link mode, we do not add anything!!!
            // the more-link is added externally
            if (
                (
                    (count($specialItemsArray) > 0)
                    && (! $isMoreLink)
                )
            ){

                $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) : 40;
                $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) : 0;
                $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) : 8;
                $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) : 10;

                $resultArray[] = array(
                    'item' => '',//$specialResults->getFirst(),
                    'cssClassSize' => 'small',
                    'cssClass' => 'related',
                    'boxType' => 4,
                    'wrapOpen' => FALSE,
                    'wrapClose' => TRUE,
                    'headerCrop' => $headerCrop,
                    'contentCrop' => $contentCrop,
                    'footerCrop' => $footerCrop,
                    'flagCrop' => $flagCrop
                );

                // @toDo !!!!!!!!!!!!!!!!
                // $specialResults->removeFirst();
                $counter++;


            // Build more link
            } else if (
                ($isMoreLink)
                && (count($this->dataArray) > $maxResults)
            ) {

                $headerCrop = ($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['headerCrop']['small']) : 40;
                $contentCrop = ($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['contentCrop']['small']) : 0;
                $footerCrop = ($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['footerCrop']['small']) : 8;
                $flagCrop = ($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) ? intval($this->settings['search']['boxes'][$this->type]['flagCrop']['small']) : 10;

                $resultArray[] = array(
                    'cssClassSize' => 'small',
                    'cssClass' => 'ajax next-page',
                    'boxType' => 5,
                    'wrapOpen' => FALSE,
                    'wrapClose' => TRUE,
                    'headerCrop' => $headerCrop,
                    'contentCrop' => $contentCrop,
                    'footerCrop' => $footerCrop,
                    'flagCrop' => $flagCrop
                );
                $counter++;
            }

        }

    }


    /**
     * Checks if item is a box that has to be displayed as big box
     *
     * @param integer $start Position in the array from where we start to check for candidates
     * @param integer $end Position in the array where we stop to check for candidates
     *
     * @return boolean
     */
    protected function isPermanentBigBox ($start, $end) {

        foreach (range($start, $end) as $key) {

            if (
                ($this->dataArray->getElement($key) instanceof \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface)
                && ($item = $this->dataArray->getElement($key))
            ){

                if (in_array(strtolower($item->getBoxTemplateName()), $this->nonSmallItemTypes))
                    return TRUE;
                    //===
            }

        }
        return FALSE;
        //===
    }
}