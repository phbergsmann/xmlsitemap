<?php

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['xmlsitemap']['urlprovider']['pages'] = array(
		'classname' => 'Tx_Xmlsitemap_Urlprovider_pages',
		'name' => 'Pages',
		'extKey' => $_EXTKEY
	);
	
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['xmlsitemap']['urlprovider']['news'] = array(
		'classname' => 'Tx_Xmlsitemap_Urlprovider_News',
		'name' => 'News',
		'extKey' => $_EXTKEY
	);

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_Xmlsitemap_Scheduler_Scheduler'] = array(
		'extension' => $_EXTKEY,
		'title' => 'XMLSitemap task',
		'description' => 'This task generates the Sitemap-XML-files',
		'additionalFields' => 'Tx_Xmlsitemap_Scheduler_Additionalfieldprovider',
	);
	
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['getHost'][] = 'EXT:xmlsitemap/Classes/Hooks/Realurl.php:Tx_Xmlsitemap_Hooks_Realurl->getHost';
?>