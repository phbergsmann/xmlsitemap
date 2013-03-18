<?php

########################################################################
# Extension Manager/Repository config file for ext "xmlsitemap".
#
# Auto generated 24-05-2011 12:44
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'XML Sitemap',
	'description' => 'Creates Sitemaps XML format-file for search engines. Includes URL-Provider for tt_news and pages',
	'category' => 'fe',
	'author' => 'Philipp Bergsmann',
	'author_email' => 'p.bergsmann@opendo.at',
	'shy' => '',
	'dependencies' => 'scheduler',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'opendo GmbH',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'scheduler' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:5:{s:9:"ChangeLog";s:4:"7ed5";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:19:"doc/wizard_form.dat";s:4:"cc61";s:20:"doc/wizard_form.html";s:4:"7136";}',
);

?>