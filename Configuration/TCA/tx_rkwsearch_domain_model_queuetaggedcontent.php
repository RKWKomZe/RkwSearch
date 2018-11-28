<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_rkwsearch_domain_model_queuetaggedcontent', 'EXT:rkw_search/Resources/Private/Language/locallang_csh_tx_rkwsearch_domain_model_queuetaggedcontent.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_rkwsearch_domain_model_queuetaggedcontent');
$GLOBALS['TCA']['tx_rkwsearch_domain_model_queuetaggedcontent'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_queuetaggedcontent',
		'label' => 'serialized',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'readOnly' => 1,
		'adminOnly' => 1,
		'hideTable' => 1,

		'enablecolumns' => array(

		),
		'searchFields' => 'serialized,rid_mapping_uid,',
		'iconfile' => 'EXT:rkw_search/Resources/Public/Icons/tx_rkwsearch_domain_model_queuetaggedcontent.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'status, message, serialized, rid_mapping',
	),
	'types' => array(
		'1' => array('showitem' => 'status, message, serialized, rid_mapping, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
        'status' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_queuetaggedcontent.status',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'int,trim'
            ),
        ),
        'message' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_queuetaggedcontent.message',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
		'serialized' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_queuetaggedcontent.serialized',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),
		'rid_mapping' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:rkw_search/Resources/Private/Language/locallang_db.xlf:tx_rkwsearch_domain_model_queuetaggedcontent.rid_mapping',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_rkwsearch_domain_model_ridmapping',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		
	),
);
