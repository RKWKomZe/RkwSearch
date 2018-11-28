<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwsearch_domain_model_ridmapping', 'EXT:rkw_search/Resources/Private/Language/locallang_csh_tx_rkwsearch_domain_model_ridmapping.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwsearch_domain_model_ridmapping');
$GLOBALS['TCA']['tx_rkwsearch_domain_model_ridmapping'] = array(
    'ctrl' => array(
        'title'	=> 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping',
        'label' => 'class',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'readOnly' => 1,
        'adminOnly' => 1,
        'hideTable' => 1,

        'enablecolumns' => array(),

        'searchFields' => 'class,rid,t3table,t3id,checksum,',
        'iconfile' => 'EXT:rkw_search/Resources/Public/Icons/tx_rkwsearch_domain_model_ridmapping.gif'
    ),
    'interface' => array(
            'showRecordFieldList' => 'class, rid, t3table, t3id, t3pid, t3lid, import_tstamp, tag_tstamp, analyse_tstamp,index_tstamp, checksum',
    ),
    'types' => array(
            '1' => array('showitem' => 'class, rid, t3table, t3id, t3pid, t3lid, import_tstamp, tag_tstamp, analyse_tstamp,index_tstamp, checksum'),
    ),
    'palettes' => array(
            '1' => array('showitem' => ''),
    ),
    'columns' => array(

        'index_tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.index_tstamp',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            )
        ),

        'analyse_tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.analyse_tstamp',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            )
        ),

        'tag_tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.tag_tstamp',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            )
        ),
        'import_tstamp' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.import_tstamp',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            )
        ),
        'class' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.class',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'rid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.rid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        't3table' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.t3table',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        't3id' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.t3id',
            'config' => array(
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,trim'
            )
        ),
        't3pid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.t3pid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            ),
        ),
        't3lid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.t3lid',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            ),
        ),
        'checksum' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.checksum',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

        'relation_checksums' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.relation_checksums',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

        'status' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.status',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

        'message' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.message',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

        'debug' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.debug',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

        'no_search' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_ridmapping.no_search',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),

    ),
);
