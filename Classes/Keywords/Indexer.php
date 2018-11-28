<?php
namespace RKW\RkwSearch\Keywords;
use \RKW\RkwSearch\OrientDb\Helper\Common;
use RKW\RkwSearch\FuzzySearch\ColognePhonetic;

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
 * Class Indexer
 *
 * @package RKW_RkwSearch
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel, RKW Kompetenzzentrum
 * @licence http://www.gnu.org/copyleft/gpl.htm GNU General Public License, version 2 or later
 */

class Indexer {


    /**
     * @var array Contains the data to be indexed
     */
    protected $data;


    /**
     * @var \RKW\RkwSearch\Domain\Model\RidMapping The mapping model
     */
    protected $mappingModel;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\ModelInterface Contains the model of the current data set
     */
    protected $contentModel;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariationsInterface Contains the variation model
     */
    protected $keywordVariationsModel;


    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentRepositoryInterface Contains the content repository
     */
    protected $contentRepository;

    /**
     * @var \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepositoryInterface Contains the variation repository
     */
    protected $keywordVariationsRepository;


    /**
     * @var array Contains the configuration from TCA
     */
    protected $configuration;


    /**
     * Get content model
     *
     * @param integer &$queryCount Counts the number of queries
     * @param integer &$keywordCount Counts the number of procecced keywords
     * @param integer $keywordLimit Maximum number of keywords to process
     * @param integer $queryLimit Maximum number of queries to process
     * @returns void
     * @throws \RKW\RkwSearch\Exception
     */
    public function index (&$queryCount = 0, &$keywordCount = 0, $queryLimit = 0, $keywordLimit = 0) {

        if (! count($this->data))
            throw new \RKW\RkwSearch\Exception ('No data to process.', 1422956008);
            //===

        /*  structure of the array-items processed here:
            [erfolg selbständige] => Array
            (
                [variations] => Array
                    (
                        [erfolg selbstständigen] => Erfolg Selbstständigen
                    )

                [count] => 1
                [distance] => 4
                [length] => 2
                [weight] => 0.088914649648271
                [tags] => NN NN
                [type] => default
            )
        */
        foreach ($this->data as $keyword => $keywordArray) {

            // break if keywords per entry is reached
            $keywordCount++;
            if ($keywordLimit)
                if ($keywordCount >= $keywordLimit)
                    break;
                    //===

            // break if query limit is reached
            if ($queryLimit)
                if ($queryCount >= $queryLimit)
                    break;
                    //===

            // check keyword structure
            if (
                (! $keywordArray['length'])
                || (! is_numeric($keywordArray['weight']))

            )
                throw new \RKW\RkwSearch\Exception ('Invalid data structure.', 1422958041);
                //===

            // ================================
            // Handle keyword variations
            // ================================
            foreach ($keywordArray['variations'] as $lowerCaseName => $caseSensitiveName) {

                $this->getKeywordVariationsModel()->setName($lowerCaseName);
                $this->getKeywordVariationsModel()->setNameBase($keyword);
                $this->getKeywordVariationsModel()->setNameCaseSensitive($caseSensitiveName);
                $this->getKeywordVariationsModel()->setNameFuzzy(ColognePhonetic::encode($lowerCaseName));
                $this->getKeywordVariationsModel()->setNameBaseFuzzy(ColognePhonetic::encode($keyword));

                $this->getKeywordVariationsModel()->setTags($keywordArray['tags']);
                $this->getKeywordVariationsModel()->setKeywordType($keywordArray['type']);
                $this->getKeywordVariationsModel()->setLanguageUid($this->getContentModel()->getLanguageUid());
                $this->getKeywordVariationsModel()->setNameLength(intval(substr_count($lowerCaseName, ' ') + 1));

                // Create variation-keyword if needed - this may happen even if the keyword already exists
                $variationVertexResult = $this->getKeywordVariationsRepository()->add($this->getKeywordVariationsModel(), FALSE, $queryCount);

                // ================================
                //  Create edge from content to variation
                // ================================
                if ($variationVertexResult) {
                    $this->getKeywordVariationsRepository()->relate($variationVertexResult, $this->getContentModel(), NULL, $this->getKeywordEdgeClass('keyword2content'), $keywordArray['weight'], TRUE);
                    $queryCount++;
                }
            }
        }
    }



    /**
     * Clean up unconnected keywords
     *
     * @param integer $limit
     * @returns integer
     */
    public function cleanup ($limit = 500) {

        $result = 0;
        $result += $this->getKeywordVariationsRepository()->cleanup($this->getKeywordEdgeClass('keyword2content'), $limit);

        return $result;
        //===

    }


    /**
     * Unrelate all keywords
     *
     * @returns boolean
     */
    public function unrelateAll () {

        return $this->getContentRepository()->unrelateAll($this->getContentModel(), $this->getKeywordEdgeClass('keyword2content'), $this->getKeywordVertexClass());
        //===

    }



    /**
     * Set data
     *
     * @param \RKW\RkwSearch\Collection\AnalysedKeywords $data
     * @returns boolean
     * @throws \RKW\RkwSearch\Exception
     */
    public function setData ($data) {

        if (! $data instanceof \RKW\RkwSearch\Collection\AnalysedKeywords)
            throw new \RKW\RkwSearch\Exception ('Invalid data given.', 1422957121);
            //===

        $this->data = $data;
        return TRUE;
        //===

    }


    /**
     * Get mapping model
     *
     * @returns \RKW\RkwSearch\Domain\Model\RidMapping
     * @throws \RKW\RkwSearch\Exception
     */
    public function getMappingModel () {

        if ($this->mappingModel)
            return $this->mappingModel;
            //===

        throw new \RKW\RkwSearch\Exception ('No valid mapping-model set.', 1422950636);
        //===

    }


    /**
     * Get content model
     *
     * @returns \RKW\RkwSearch\OrientDb\Domain\Model\DocumentInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getContentModel () {

        if (! $this->contentModel) {

            if (
                ($contentClass = $this->getMappingModel()->getClass())
                && ($contentModelName = Common::getOrientModelFromClassName($contentClass))
                && (class_exists($contentModelName))
            ) {

                $this->contentModel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($contentModelName);
                $this->contentModel->setRid($this->getMappingModel()->getRid());
                $this->contentModel->setLanguageUid($this->getMappingModel()->getT3lid());

            } else {

                throw new \RKW\RkwSearch\Exception ('Content model could not be set.', 1422951235);
                //===
            }
        }

        return $this->contentModel;
        //===

    }



    /**
     * Get keyword variation model
     *
     * @returns \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariationsInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getKeywordVariationsModel () {

        if (! $this->keywordVariationsModel) {

            if (
                ($class = $this->getKeywordVertexClass())
                && ($modelName = Common::getOrientModelFromClassName($class))
                && (class_exists($modelName))
                && ($model = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($modelName))
                && ($model instanceof \RKW\RkwSearch\OrientDb\Domain\Model\KeywordVariationsInterface)
            ) {

                $this->keywordVariationsModel = $model;

            } else {

                throw new \RKW\RkwSearch\Exception ('Variation model could not be set.', 1422952611);
                //===
            }
        }

        return $this->keywordVariationsModel;
        //===

    }



    /**
     * Get keyword bases repository
     *
     * @returns \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentRepositoryInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getContentRepository () {

        if (! $this->contentRepository) {

            if (
                ($class = $this->getMappingModel()->getClass())
                && ($repositoryName = Common::getOrientRepositoryFromClassName($class))
                && (class_exists($repositoryName))
                && ($repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($repositoryName))
                && ($repository instanceof \RKW\RkwSearch\OrientDb\Domain\Repository\DocumentRepositoryInterface)
            ) {

                // deactivate automatic cache clean up
                $repository->setClearCache(FALSE);
                $this->contentRepository = $repository;

            } else {

                throw new \RKW\RkwSearch\Exception ('Content repository could not be set.', 1424711769);
                //===
            }
        }

        return $this->contentRepository;
        //===

    }


    /**
     * Get keyword variation repository
     *
     * @returns \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepositoryInterface
     * @throws \RKW\RkwSearch\Exception
     */
    public function getKeywordVariationsRepository () {

        if (! $this->keywordVariationsRepository) {

            if (
                ($class = $this->getKeywordVertexClass())
                && ($repositoryName = Common::getOrientRepositoryFromClassName($class))
                && (class_exists($repositoryName))
                && ($repository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($repositoryName))
                && ($repository instanceof \RKW\RkwSearch\OrientDb\Domain\Repository\KeywordVariationsRepositoryInterface)
            ) {

                $repository->setClearCache(FALSE);
                $this->keywordVariationsRepository = $repository;

            } else {

                throw new \RKW\RkwSearch\Exception ('Variation repository could not be set.', 1422953760);
                //===
            }
        }

        return $this->keywordVariationsRepository;
        //===

    }



    /**
     * Set mapping model
     *
     * @param \RKW\RkwSearch\Domain\Model\RidMapping Mapping Model
     * @returns $this
     * @throws \RKW\RkwSearch\Exception
     */
    public function setMappingModel ($mappingModel) {

        if (! $mappingModel instanceof \RKW\RkwSearch\Domain\Model\RidMapping)
            throw new \RKW\RkwSearch\Exception ('Given model is not of type \RKW\RkwSearch\Domain\Model\RidMapping.', 1422949560);
            //===

        if (! $mappingModel->getRid())
            throw new \RKW\RkwSearch\Exception ('Given model has no rid.', 1422950104);
            //===

        if (! $mappingModel->getClass())
            throw new \RKW\RkwSearch\Exception ('Given model has no class.', 1422950431);
            //===

        if (is_null($mappingModel->getT3lid()))
            throw new \RKW\RkwSearch\Exception ('Given model has no t3lid set.', 1422950488);
            //===

        $this->mappingModel = $mappingModel;

        // reset all other model stuff
        // this is important since we are now using another mapping model
        // and therefore all other data has to be reset to!!!
        unset($this->contentModel);
        unset($this->keywordVariationsModel);
        unset($this->contentRepository);

        return $this;
        //===

    }



    /**
     *  Constructor
     */
    public function __construct ($configuration = array()) {

        // set given configuration (if given)
        if (!empty ($configuration))
            $this->configuration = $configuration;
    }



    /**
     * Get configuration
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getConfiguration() {

        // load from TypoScript
        if (! $this->configuration) {

            $settings = Common::getTyposcriptConfiguration();
            if ($settings['indexing'])
                $this->configuration = $settings['indexing'];
        }

        if (! $this->configuration)
            throw new \RKW\RkwSearch\Exception ('No valid setup found.', 1422951682);
            //===

        return $this->configuration;
        //===
    }


    /**
     * Get configuration of keyword vertex class
     *
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getKeywordVertexClass() {

        $configuration = $this->getConfiguration();
        if (
            ($configuration['keywords'])
            && ($configuration['keywords']['vertexClass'])
        )
            return $configuration['keywords']['vertexClass'];
            //===

        throw new \RKW\RkwSearch\Exception ('No valid configuration for keyword vertex class found.', 1422952007);
        //===

    }

    /**
     * Get configuration of keyword edge class
     *
     * @param string $key The item-key to return
     * @return array
     * @throws \RKW\RkwSearch\Exception
     */
    protected function getKeywordEdgeClass($key) {

        $configuration = $this->getConfiguration();
        if (
            ($configuration['keywords'])
            && ($configuration['keywords']['edgeClass'])
            && (is_array($configuration['keywords']['edgeClass']))
            && ($configuration['keywords']['edgeClass'][$key])
        )
            return $configuration['keywords']['edgeClass'][$key];
            //===

        throw new \RKW\RkwSearch\Exception (sprintf('No valid configuration for keyword edge class with key %s found.', $key), 1422952007);
        //===

    }


   

}