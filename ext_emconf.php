<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "rkw_search"
 *
 * Auto generated by Extension Builder 2014-04-03
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'RKW Search',
	'description' => 'Search engine based on OrientDB',
	'category' => 'plugin',
	'author' => 'Steffen Kroggel',
	'author_email' => 'developer@steffenkroggel.de',
	'author_company' => 'RKW Kompetenzzentrum',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '7.6.8',
	'constraints' => array(
		'depends' => array(
			'extbase' => '7.6.0-7.6.99',
			'fluid' => '7.6.0-7.6.99',
			'typo3' => '7.6.0-7.6.99',
            'rkw_basics' => '7.6.10-8.7.99',
            'rkw_authors' => '7.6.10-8.7.99',
            'rkw_projects' => '7.6.10-8.7.99',
			'rkw_geolocation' => '7.6.10-8.7.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>