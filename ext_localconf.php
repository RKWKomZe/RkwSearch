<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// FE-Plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'RKW.' . $_EXTKEY,
    'Rkwsearch',
    array(
        /* @toDo: Finally delete when tested
        'Search' => 'search, pageNotFound, recent, recentArticle, events, publications, publicationsSpecial, publicationSeries, series, consultants, consultantsInternal, blog, example, home, news, related, relatedNoCache, test, mostSearchedKeywords',
         */
        'Search' => 'search, pageNotFound, recent, recentArticle, events, publications, publicationsSpecial, publicationSeries, series, blog, example, home, news, related, relatedNoCache, test, mostSearchedKeywords',

    ),
    // non-cacheable actions
    array(
        /* @toDo: Finally delete when tested
        'Search' => 'search, pageNotFound, home, news, recent, recentArticle, events, publications, publicationsSpecial, consultants, consultantsInternal, relatedNoCache, example, test',
         */
        'Search' => 'search, pageNotFound, home, news, recent, recentArticle, events, publications, publicationsSpecial, relatedNoCache, example, test',


    )
);

// register command controller (cronjob)
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'RKW\\RkwSearch\\Controller\\SearchCommandController';

// register hook for backend save
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = 'RKW\\RkwSearch\\Hooks\\ImportDatahandlerHook';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][$_EXTKEY] = 'RKW\\RkwSearch\\Hooks\\ImportDatahandlerHook';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass'][$_EXTKEY] = 'RKW\\RkwSearch\\Hooks\\ImportDatahandlerHook';

// signal slots for sys_file_metadata
/*
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\Index\\MetaDataRepository',
    'recordCreated',
    'RKW\\RkwSearch\\Hooks\\ImportFileMetadataHook',
    'recordCreatedSlot',
    FALSE
);

$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\Index\\MetaDataRepository',
    'recordDeleted',
    'RKW\\RkwSearch\\Hooks\\ImportFileMetadataHook',
    'recordDeletedSlot',
    FALSE
);
*/

// Signal Slot for varnish-extension
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('varnish')) {

    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher
     */
    $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
    $signalSlotDispatcher->connect(
        'RKW\\RkwSearch\\Controller\\SearchCommandController',
        \RKW\RkwSearch\Controller\SearchCommandController::SIGNAL_CLEAR_PAGE_VARNISH,
        'RKW\\RkwSearch\\Service\\VarnishService',
        'clearCacheOfPageEvent'
    );

    $signalSlotDispatcher->connect(
        'RKW\\RkwSearch\\Hooks\\ImportDatahandlerHookAbstract',
        \RKW\RkwSearch\Hooks\ImportDatahandlerHookAbstract::SIGNAL_CLEAR_PAGE_VARNISH,
        'RKW\\RkwSearch\\Service\\VarnishService',
        'clearCacheOfPageEvent'
    );
}


// set cache for search
if (! is_array($TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY])) {
    $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][$_EXTKEY] = array();
}

// set logger
$GLOBALS['TYPO3_CONF_VARS']['LOG']['RKW']['RkwSearch']['writerConfiguration'] = array(

    // configuration for WARNING severity, including all
    // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
    \TYPO3\CMS\Core\Log\LogLevel::WARNING => array(
        // add a FileWriter
        'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => array(
            // configuration for the writer
            'logFile' => 'typo3temp/logs/tx_rkwsearch.log'
        )
    ),
);

// Add rootline-Fields
$TYPO3_CONF_VARS['FE']['addRootLineFields'] .= ',tx_rkwsearch_pubdate';

#===================================================================================
# Orient DB
#===================================================================================

if (! is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']))
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB'] = array ();


$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['typo3TableMapping'] = array (
    'pages' => 'DocumentPages',
    'sys_category' => 'DocumentCategory',
    'tx_rkwauthors_domain_model_authors' => 'DocumentAuthors',
    'tx_rkwbasics_domain_model_documenttype' => 'DocumentTypes',
    'tx_rkwbasics_domain_model_series' => 'DocumentSeries',
    'tx_rkwbasics_domain_model_enterprisesize' => 'DocumentEnterpriseSize',
    'tx_rkwbasics_domain_model_sector' => 'DocumentSector',
    'tx_rkwbasics_domain_model_department' => 'DocumentDepartments',
    'tx_rkwprojects_domain_model_projects' => 'DocumentProjects',
    /* @toDo: Finally delete when tested
    'tx_rkwconsultant_domain_model_consultant' => 'DocumentConsultants',
    'tx_rkwconsultant_domain_model_consultantservice'  => 'DocumentConsultantsService',
    'tx_rkwconsultant_domain_model_basicservice'  =>  'DocumentConsultantsBasicService',
     */
);


$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['rkw_search']['OrientDB']['TCA'] = array (


    //===============================================
/*
    'FileMetadata' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
        ),

        'columns' => array (
            'title' => array (
                'mappingField' => 'title'
            ),
            'description' => array (
                'mappingField' => 'description'
            ),
            'file' => array (
                'mappingField' => 'file'
            ),
        ),

        'relations' => array (
            'tx_rkwprojects_project_uid' => array(
                'edgeClass'  => 'EdgeProject',
            ),
        ),
    ),
*/

    //===============================================

    'DocumentAbstract' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'noSearch' => 'noSearch',
            'languageField'  => 'sysLanguageUid',
            'dokType' => 'doktype',
            'dokTypeList' => '0,1',
            'searchField' => 'searchContent',
            'searchFieldFuzzy' => 'searchContentFuzzy',
            'searchFieldType' => 'searchContentType',
            'searchFieldSize' => 'searchContentSize',
            'searchFieldTitle' => 'searchContentTitle',
            'enablecolumns' => array (
                'disabled' => 'hidden',
            )
        ),

    ),

    //===============================================

    'DocumentPages' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'noSearch' => 'noSearch',
            'languageField'  => 'sysLanguageUid',
            'transOrigPointerField' => 'pid',
            'dokType' => 'doktype',
            'dokTypeList' => '1',
            'searchField' => 'searchContent',
            'searchFieldFuzzy' => 'searchContentFuzzy',
            'pdfImportParentEdge' => 'EdgeImportParent',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),


        'columns' => array (
            'doktype' => array (
                'mappingField' => 'doktype'
            ),
            'title' => array (
                'mappingField' => 'title'
            ),
            'subtitle' => array (
                'mappingField' => 'subtitle'
            ),
            'tx_rkwbasics_teaser_text' => array (
                'mappingField' => 'abstract'
            ),
            'description' => array (
                'mappingField' => 'description'
            ),
            'keywords' => array (
                'mappingField' => 'keywords'
            ),
            'tx_bmpdf2content_is_import' => array (
                'mappingField' => 'pdfImport'
            ),
            'tx_bmpdf2content_is_import_sub' => array (
                'mappingField' => 'pdfImportSub'
            ),
            'tx_rkwsearch_pubdate' => array (
                'mappingField' => 'pubdate'
            ),
        ),

        'relations' => array (

            'categories' => array (
                'edgeClass'  => 'EdgeCategory',
                'mmTable' => 'sys_category_record_mm',
                'foreignTable' => 'sys_category'
            ),


            'tx_rkwauthors_authorship' => array (
                'edgeClass'  => 'EdgeAuthor',
            ),

	        'tx_rkwbasics_document_type' => array(
                'edgeClass'  => 'EdgeType',
            ),

            'tx_rkwbasics_series' => array(
                'edgeClass'  => 'EdgeSeries',
            ),

            'tx_rkwbasics_enterprisesize' => array(
                'edgeClass'  => 'EdgeEnterpriseSize',
            ),

            'tx_rkwbasics_sector' => array(
                'edgeClass'  => 'EdgeSector',
            ),

            'tx_rkwbasics_department' => array(
                'edgeClass'  => 'EdgeDepartment',
            ),

            'tx_rkwprojects_project_uid' => array(
                'edgeClass'  => 'EdgeProject',
            ),

            /*
            'tx_rkwbasics_file' => array(
                'edgeClass'  => 'EdgeDownload',
                'foreignTable' => 'sys_file_metadata',
            ),
            */
        )
    ),

    //===============================================

    'DocumentAuthors' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'firstname',
            'searchFieldFuzzy' => 'firstnameFuzzy',
            'searchFieldTwo' => 'lastname',
            'searchFieldTwoFuzzy' => 'lastnameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'first_name' => array (
                'mappingField' => 'firstname'
            ),
            'last_name' => array (
                'mappingField' => 'lastname'
            ),
            'middle_name' => array (
                'mappingField' => 'middlename',
            ),
            'title_before' => array (
                'mappingField' => 'titleBefore'
            ),
            'title_after' => array (
                'mappingField' => 'titleAfter'
            ),
            'email' => array (
                'mappingField' => 'email'
            ),
            'internal' => array (
                'mappingField' => 'internal'
            ),
        ),

    ),


    //===============================================

    'DocumentProjects' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'searchFieldTwo' => 'shortName',
            'searchFieldTwoFuzzy' => 'shortNameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),


        'columns' => array (
            'name' => array (
                'mappingField' => 'name'
            ),
            'short_name' => array (
                'mappingField' => 'shortName'
            ),
        ),

    ),

    //===============================================

    'DocumentTypes' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'name' => array (
                'mappingField' => 'name'
            ),
            'box_template_name' => array (
                'mappingField' => 'boxTemplateName'
            )
        ),

    ),

    //===============================================

    'DocumentSeries' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'name' => array (
                    'mappingField' => 'name'
            ),
        ),
    ),


    //===============================================

    'DocumentEnterpriseSize' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'name' => array (
                'mappingField' => 'name'
            ),
        ),
    ),


    //===============================================

    'DocumentSector' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                    'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'name' => array (
                    'mappingField' => 'name'
            ),
        ),
    ),

    //===============================================

    'DocumentDepartments' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (
            'name' => array (
                'mappingField' => 'name'
            ),
            'css_class' => array (
                'mappingField' => 'cssClass'
            )
        ),
    ),

    //===============================================

    'DocumentEvents' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'sortby' => 'sorting',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'name',
            'searchFieldFuzzy' => 'nameFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),

        'columns' => array (

        ),
    ),


    //===============================================
    /* @toDo: Finally delete when tested
    'DocumentConsultants' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'company',
            'searchFieldFuzzy' => 'companyFuzzy',
        ),


        'columns' => array (
            'company' => array (
                'mappingField' => 'company'
            ),
            'address' => array (
                'mappingField' => 'address'
            ),
            'zip' => array (
                'mappingField' => 'zip'
            ),
            'city' => array (
                'mappingField' => 'city'
            ),
            'telephone' => array (
                'mappingField' => 'telephone'
            ),
            'fax' => array (
                'mappingField' => 'fax'
            ),
            'email' => array (
                'mappingField' => 'email'
            ),
            'www' => array (
                'mappingField' => 'www'
            ),
            'latitude' => array (
                'mappingField' => 'latitude'
            ),
            'longitude' => array (
                'mappingField' => 'longitude'
            ),
            'short_description' => array (
                'mappingField' => 'shortDescription'
            ),
            'rkw_network' => array (
                'mappingField' => 'rkwNetwork'
            ),
        ),


        'relations' => array (

            'consultant_service' => array (
                'edgeClass'  => 'EdgeService',
            ),

        )

    ),

    //===============================================

    'DocumentConsultantsService' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
        ),


        'columns' => array (
            'further_informations' => array (
                'mappingField' => 'furtherInformation'
            ),

        ),

        'relations' => array (

            'basic_service' => array (
                'edgeClass'  => 'EdgeService',
            ),

        )
    ),

    //===============================================

    'DocumentConsultantsBasicService' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'title',
            'searchFieldFuzzy' => 'titleFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),


        'columns' => array (
            'title' => array (
                'mappingField' => 'title'
            ),
        ),
    ),
    */
    //===============================================

    'DocumentCategory' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
            'cruser_id' => 'cruser_id',
            'searchField' => 'title',
            'searchFieldFuzzy' => 'titleFuzzy',
            'delete' => 'deleted',
            'enablecolumns' => array (
                'disabled' => 'hidden'
            )
        ),


        'columns' => array (
            'title' => array (
                'mappingField' => 'title'
            ),
        ),
    ),

    //===============================================
    /*
    'EdgeDownload' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),
    */


    //===============================================

    'EdgeProject' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeDepartment' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeType' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeSeries' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeAuthor' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeContains' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'weight' => 'weight'
        ),
    ),

    //===============================================
    /* @toDo: Finally delete when tested

        'EdgeService' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),
     * */

    //===============================================

    'EdgeImportParent' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'EdgeCategory' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
        ),
    ),

    //===============================================

    'KeywordVariations' => array (

        'ctrl' => array (
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'languageField'  => 'sysLanguageUid',
            'fuzzyAppendix' => 'fuzzy',
        ),
    ),

);

