<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempPagesColumns = array(

    'tx_rkwsearch_pubdate' => array(
        /*'displayCond' =>'USER:RKW\\RkwSearch\\UserFunctions\\TcaDisplayCondition->displayIfInRootLine',*/
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_pubdate',
        'config' => array(
            'type' => 'input',
            'size' => 13,
            'max' => 20,
            'eval' => 'datetime',
            'checkbox' => 0,
        ),
    ),

    'tx_rkwsearch_index_timestamp' => array(
        'displayCond' =>'USER:RKW\\RkwSearch\\UserFunctions\\TcaDisplayCondition->displayIfInRootLine',
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_timestamp',
        'config' => array(
            'type' => 'input',
            'size' => 10,
            'readOnly' =>1,
            'eval' => 'datetime',
            'checkbox' => 1,
            'default' => time()
        )
    ),
    'tx_rkwsearch_index_status' => array(
        'displayCond' =>'USER:RKW\\RkwSearch\\UserFunctions\\TcaDisplayCondition->displayIfInRootLine',
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status',
        'config' => array(
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => array(
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.zero', 0),
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.one', 1),
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.two', 2),
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.three', 3),
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.four', 4),
                array('LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_status.I.error', 99),

            ),
            'size' => 1,
            'readOnly' =>1,
            'maxitems' => 1,
            'eval' => ''
        ),
    ),
    'tx_rkwsearch_index_result' => array(
        'displayCond' =>'USER:RKW\\RkwSearch\\UserFunctions\\TcaDisplayCondition->displayIfInRootLine',
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_index_result',
        'config' => array(
            'type' => 'text',
            'readOnly' => 1,
            'pass_content' => 1,
            'cols' => 40,
            'rows' => 60,
            'eval' => 'trim'
        )
    ),
    'tx_rkwsearch_no_search' => array(
        'displayCond' =>'USER:RKW\\RkwSearch\\UserFunctions\\TcaDisplayCondition->displayIfInRootLine',
        'exclude' => 0,
        'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_no_search',
        'config' => array(
            'type' => 'check',
            'items' => array(
                '1' => array(
                    '0' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_pages.tx_rkwsearch_no_search.I.deactivated'
                )
            ),
            'size' => 1,
            'maxitems' => 1,
            'eval' => ''
        ),
    ),
);


// Add TCA
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$tempPagesColumns);

unset($tempPagesColumns['tx_rkwsearch_pubdate']);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages_language_overlay',$tempPagesColumns);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', 'tx_rkwsearch_pubdate,--linebreak--', '1,3,4,7', 'before:tx_rkwbasics_teaser_text');

// Add fields to existing palette (created by rkw_basics)
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'tx_rkwbasics_common','tx_rkwsearch_no_search,--linebreak--', 'after:tx_rkwbasics_document_type');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages_language_overlay', 'tx_rkwbasics_common','tx_rkwsearch_no_search,--linebreak--');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'tx_rkwbasics_extended2','tx_rkwsearch_index_timestamp,tx_rkwsearch_index_status,--linebreak--,tx_rkwsearch_index_result');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages_language_overlay', 'tx_rkwbasics_extended2','tx_rkwsearch_index_timestamp,tx_rkwsearch_index_status,--linebreak--,tx_rkwsearch_index_result');

