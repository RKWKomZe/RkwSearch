#
# Tabellenstruktur für Tabelle cf_rkw_search_tags
#

CREATE TABLE cf_rkw_search_tags (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  identifier varchar(250) NOT NULL DEFAULT '',
  tag varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  KEY cache_id (identifier),
  KEY cache_tag (tag)
);


#
# Tabellenstruktur für Tabelle cf_rkw_search
#
CREATE TABLE cf_rkw_search (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  identifier varchar(250) NOT NULL DEFAULT '',
  expires int(11) unsigned NOT NULL DEFAULT '0',
  content mediumblob,
  PRIMARY KEY (id),
  KEY cache_id (identifier,expires)
);



#
# Table structure for table 'tx_rkwsearch_domain_model_ridmapping'
#
CREATE TABLE tx_rkwsearch_domain_model_ridmapping (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	class varchar(255) DEFAULT '' NOT NULL,
	rid varchar(255) DEFAULT '' NOT NULL,
	t3table varchar(255) DEFAULT '' NOT NULL,
	t3id int(11) DEFAULT '0' NOT NULL,
	t3pid int(11) DEFAULT '0' NOT NULL,
	t3lid int(11) DEFAULT '0' NOT NULL,
	checksum varchar(255) DEFAULT '' NOT NULL,
	relation_checksums text,


	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	import_tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	tag_tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	analyse_tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	index_tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	status int(11) unsigned DEFAULT '0',
	message varchar(255) DEFAULT '' NOT NULL,
	debug int(11) unsigned DEFAULT '0',
	no_search int(11) unsigned DEFAULT '0',
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
    UNIQUE KEY rid (rid),
    UNIQUE KEY t3table (t3table,t3id,t3lid),
    KEY parent (pid)
);



#
# Table structure for table 'tx_rkwsearch_domain_model_queuetaggedcontent'
#
CREATE TABLE tx_rkwsearch_domain_model_queuetaggedcontent (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	rid_mapping int(11) unsigned DEFAULT '0',
	serialized longtext NOT NULL,
	status int(11) unsigned DEFAULT '0',
	message varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),


);


#
# Table structure for table 'tx_rkwsearch_domain_model_queueanalysedkeywords'
#
CREATE TABLE tx_rkwsearch_domain_model_queueanalysedkeywords (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	rid_mapping int(11) unsigned DEFAULT '0',
	serialized longtext NOT NULL,
	keyword_count int(11) unsigned DEFAULT '0',
	status int(11) unsigned DEFAULT '0',
	message varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'pages'
#
CREATE TABLE pages (

    tx_rkwsearch_index_timestamp int(11) DEFAULT '0' NOT NULL,
	tx_rkwsearch_index_status int(11) DEFAULT '0' NOT NULL,
	tx_rkwsearch_index_result text NOT NULL,
    tx_rkwsearch_no_search int(11) DEFAULT '0' NOT NULL,
    tx_rkwsearch_pubdate int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (

    tx_rkwsearch_index_timestamp int(11) DEFAULT '0' NOT NULL,
	tx_rkwsearch_index_status int(11) DEFAULT '0' NOT NULL,
	tx_rkwsearch_index_result text NOT NULL,
	tx_rkwsearch_no_search int(11) DEFAULT '0' NOT NULL,

);


#
# Table structure for table 'tx_rkwbasics_domain_model_documenttype'
#
CREATE TABLE tx_rkwbasics_domain_model_documenttype (

	search int(11) unsigned DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_rkwbasics_domain_model_department'
#
CREATE TABLE tx_rkwbasics_domain_model_department (

	search int(11) unsigned DEFAULT '0' NOT NULL,

);

