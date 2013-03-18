<?php
class Tx_Xmlsitemap_Urlprovider_News
	extends Tx_Xmlsitemap_Domain_Urlprovider 
	implements Tx_Xmlsitemap_Interfaces_Urlprovider
{
	protected $name = 'news';
	protected $db = NULL;
	protected $baseURL = '';
	
	/**
	 * 
	 * Returns a PHP-Array in the schema of the XML
	 * array(array('loc'=>URL, 'lastmod'=>Date/Time),...)
	 */
	public function getURLArray() {
		return $this->getNewsRecords();
	}
	
	public function getNewsRecords() {
		t3lib_div::devLog('exec_SELECTgetRows - getNewsRecords', 'xmlsitemap',0);
		$records = $this->getDB()->exec_SELECTgetRows('uid, tstamp, pid', 'tt_news', 'hidden=0 AND deleted=0 AND (type=0 OR type=4) AND starttime<' . time());
		t3lib_div::devLog('news records found:', 'xmlsitemap',0,$records);
		$records = $this->addTranslations($records);
		t3lib_div::devLog('after translation:', 'xmlsitemap',0,$records);
		$records = $this->normalizeRecords($records);
		t3lib_div::devLog('after normalization:', 'xmlsitemap',0,$records);
		
		return $records;
	}
	
	protected function normalizeRecords($records) {
		foreach ($records as $key => $record) {
			$records[$key]['lastmod'] = date('c', $record['tstamp']);
			
			if (!$record['_LOCALIZED_UID']) {
				t3lib_div::devLog('getTypoLink_URL without _LOCALIZED_UID - normalizeRecords', 'xmlsitemap',0);
				$records[$key]['loc'] = $this->generateURL($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_xmlsitemap.']['newsSinglePID'], '&tx_ttnews[tt_news]=' . $record['uid']);
			}
		}
		
		return $records;
	}
	
	protected function addTranslations(array $records) {
		$languageUIDs = $this->getLanguageUIDs();
		$return = $records;
		
		foreach ($records as $record) {
			foreach ($languageUIDs as $languageUID) {
				$overlayRecord = $this->getPageSelect()->getRecordOverlay('tt_news',$record,$languageUID,'hideNonTranslated');
				t3lib_div::devLog('getRecordOverlay - addTranslations', 'xmlsitemap',0);
				if ($overlayRecord) {
					$overlayRecord['loc'] = $this->generateURL(
							$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_xmlsitemap.']['newsSinglePID'], 
							'&tx_ttnews[tt_news]=' . $record['uid'] . '&L=' .$languageUID
						);
					t3lib_div::devLog('getTypoLink_URL - addTranslations', 'xmlsitemap',0);
					$return[] = $overlayRecord;
				} 
			}
		}
		
		return $return;
	}
	
	/**
	 * 
	 * Returns the name of the current Urlprovider
	 * used for naming the sitemap.xml file
	 */
	public function getName() {
		return $this->name;
	}
}
?>