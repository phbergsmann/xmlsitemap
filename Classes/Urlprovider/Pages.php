<?php
class Tx_Xmlsitemap_Urlprovider_pages
	extends Tx_Xmlsitemap_Domain_Urlprovider
	implements Tx_Xmlsitemap_Interfaces_Urlprovider
{
	protected $db = NULL;
	protected $cObj = NULL;
	protected $name = 'pages';
	protected $pageSelect = NULL;
	
	/**
	 * @see Tx_Xmlsitemap_Interfaces_Urlprovider::getName()
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * 
	 * set the used DB-object
	 * @param t3lib_DB $database
	 * @return t3lib_DB
	 */
	public function setDB(t3lib_DB $database) {
		$this->db = $database;
		
		return $this->db;
	}
	
	/**
	 * 
	 * get the used DB-object
	 * @return t3lib_DB
	 */
	public function getDB() {
		if (is_null($this->db)) {
			$this->db = $GLOBALS['TYPO3_DB'];
		}
		
		return $this->db;
	}
	
	/**
	 * get a array of the urls
	 * @see Tx_Xmlsitemap_Interfaces_Urlprovider::getURLArray()
	 * @return array array('loc'=>URL, 'lastmod'=>Date/Time)
	 */
	public function getURLArray() {
		return $this->getAllVisiblePages();
	}
	
	/**
	 * convert the rows from the db to the schema
	 * @return array array('loc'=>URL, 'lastmod'=>Date/Time)
	 */
	protected function getAllVisiblePages() {
		
		$PageRows = $this->getRowsFromDB();
		$cObj = $this->getCObject();
		
		$languageUIDs = t3lib_div::trimExplode(',',$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_xmlsitemap.']['languageUIDs']);
		
		foreach ($PageRows as $key=>$value) {
			$PageRows[$key] = array(
				'lastmod' => $this->getLastModTime($value),
				'loc' => $this->generateURL($value['uid'])
				//'loc' => $this->prefixUrl($cObj->getTypoLink_URL($value['uid'])),
			);
			
			foreach ($languageUIDs as $languageUID) {
				$overlayRec = $this->getPageSelect()->getPageOverlay($value,$languageUID);
				t3lib_div::devLog('overlay record', 'xmlsitemap',0,$overlayRec);
				if ($overlayRec['_PAGES_OVERLAY']) {
					$PageRows[] = array(
						'lastmod' => $this->getLastModTime($overlayRec, $languageUID),
						'loc' => $this->generateURL($overlayRec['uid'],'&L=' . $languageUID)
						//'loc' => htmlentities($cObj->getTypoLink_URL($overlayRec['uid'],array('L' => $languageUID))),
					);
				}
			}
			
		}
		
		return $PageRows;
	}
	
	public function getLastModTime($page, $languageUID = 0) {
		$content = $this->getDB()->exec_SELECTgetSingleRow('tstamp','tt_content','pid=' . $page['uid'] . ' AND deleted=0 AND hidden=0 AND sys_language_uid=' . $languageUID,'','tstamp DESC');
		if ($content['tstamp'] > $page['tstamp'])
			return date('c',$content['tstamp']);
		
		return date('c',$page['tstamp']);
	}
	
	public function prefixUrl($url) {
		if ($GLOBALS['TSFE']->tmpl->setup['config.']['baseURL']) {
			$url = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] . $url;
		}
		
		return $url;
	}
	
	/**
	 * 
	 * DB request
	 */
	protected function getRowsFromDB() {
		$db = $this->getDB();
		$PageRows = $db->exec_SELECTgetRows('uid, tstamp', 'pages', 'doktype=1 AND hidden=0 AND nav_hide=0 AND deleted=0 AND starttime<' . time());
		
		return $PageRows;
	}
	
	public function getPageSelect() {
		if (is_null($this->pageSelect))
			$this->pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
			
		return $this->pageSelect;
	}
	
	/**
	 * 
	 * set the cObject
	 * @param tslib_cObj $cObj
	 */
	public function setCObject(tslib_cObj $cObj) {
		$this->cObj = $cObj;
	}
	
	/**
	 * 
	 * get the cObject
	 * @return tslib_cObj
	 */
	public function getCObject() {
		if (is_null($this->cObj)) {
			$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		}
		
		return $this->cObj;
	}
}
?>