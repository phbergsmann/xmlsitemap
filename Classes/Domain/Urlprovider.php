<?php
/** COPYRIGHT NOTICE **/

/**
 * 
 * Base-class for the URL-providers. It implements basic
 * functions often used.
 * 
 * @package TYPO3
 * @subpackage tx_xmlsitemap
 * 
 * @author Philipp Bergsmann <p.bergsmann@opendo.at>
 *
 */
class Tx_Xmlsitemap_Domain_Urlprovider {
	
	/**
	 * 
	 * the cObject used for Typoscript functions
	 * @var tslib_cObj
	 */
	protected $cObject = NULL;
	
	/**
	 * 
	 * the pageSelect-object used for language overlays
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect = NULL;
	
	/**
	 * 
	 * the language-UIDs used for translations
	 * @var array
	 */
	protected $languageUIDs = NULL;
	
	/**
	 * 
	 * the databse-object used for data-aggregation
	 * @var t3lib_DB
	 */
	protected $db = NULL;
	
	/**
	 * 
	 * RealURL URL-Object
	 * @var tx_realurl
	 */
	protected $urlObj = NULL;
	
	/**
	 * 
	 * Enter description here ...
	 * @var t3lib_TStemplate
	 */
	protected $TStemplate = NULL;
	
	/**
	 * 
	 * Returns the name of the current Urlprovider
	 * used for naming the sitemap.xml file
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * 
	 * get the language-IDs used for the link-creation
	 * if not set before it explodes the language-UIDs set
	 * in plugin.tx_xmlsitemap.languageUIDs
	 * @return array
	 */
	public function getLanguageUIDs() {
		if (is_null($this->languageUIDs))
			$this->languageUIDs = t3lib_div::trimExplode(',',$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_xmlsitemap.']['languageUIDs']);
			
		return $this->languageUIDs;
	}
	
	/**
	 * 
	 * set the used language-UIDs used for the link-creation
	 * @param array $languageUIDs
	 */
	public function setLanguageUIDs($languageUIDs) {
		$this->languageUIDs = $languageUIDs;
	}
	
	/**
	 * 
	 * get the baseURL which is prependet to the links
	 * if not set before it uses config.baseURL from TypoScript
	 * @return string
	 */
	public function getBaseURL() {
		if (!$this->baseURL)
			$this->baseURL = $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'];
			
		return $this->baseURL;
	}
	
	/**
	 * 
	 * set the baseURL which is used for link-creation
	 * @param string $baseURL
	 */
	public function setBaseURL($baseURL) {
		$this->baseURL = $baseURL;
	}
	
	/**
	 * 
	 * injects the pageSelect-object
	 * @param t3lib_pageSelect $pageSelect
	 */
	public function setPageselect(t3lib_pageSelect $pageSelect) {
		$this->pageSelect = $pageSelect;
	}
	
	/**
	 * get the fitting pageSelect-object
	 * if not set before, it returns a new instance
	 * @return t3lib_pageSelect
	 */
	public function getPageselect() {
		if(is_null($this->pageSelect))
			$this->pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		
		return $this->pageSelect;
	}
	
	/**
	 * 
	 * inject the db-object
	 * @param t3lib_db $db
	 */
	public function setDB(t3lib_db $db) {
		$this->db =$db;
	}
	
	/**
	 * 
	 * get the db-object
	 * if not set before the instance from
	 * $GLOBALS['TYPO3_DB'] is used
	 * @return t3lib_db
	 */
	public function getDB() {
		if (is_null($this->db))
			$this->db = $GLOBALS['TYPO3_DB'];
		
		return $this->db;
	}
	
	/**
	 * 
	 * inject the cObject
	 * @param tslib_cObj $cObject
	 */
	public function setCObject(tslib_cObj $cObject) {
		$this->cObject = $cObject;
	}
	
	/**
	 * 
	 * Returns the cObject. If no cObject was set a new
	 * instance of tslib_cObj is returned
	 * @return tslib_cObj
	 */
	public function getCObject() {
		if (is_null($this->cObject))
			$this->cObject = t3lib_div::makeInstance('tslib_cObj');
		
		return $this->cObject;
	}
	
	/**
	 * 
	 * Generates the final URL inlcuding RealURL-Support
	 * @param string $queryString (e.g. "&xxx=1&yyy=2")
	 */
	public function generateURL($pageID, $queryString) {
		require_once(t3lib_extMgm::extPath('realurl') . 'class.tx_realurl.php');

		$urlObj = $this->getUrlobj();
		
		$urlParts = parse_url($this->getBaseURL());
		$host = strtolower($urlParts['host']);
		$urlObj->host = $host;
		
		$urlObj->extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT'];
		
		if (!$GLOBALS['TSFE']->sys_page) {
			$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		}
		if (!$GLOBALS['TSFE']->csConvObj) {
			$GLOBALS['TSFE']->csConvObj = t3lib_div::makeInstance('t3lib_cs');
		}
		if (!$GLOBALS['TSFE']->tmpl->rootLine[0]['uid']) {
			$GLOBALS['TSFE']->tmpl->rootLine[0]['uid'] = 1;
		}
		
		$pA = t3lib_div::cHashParams($queryString);
		if (count($pA)>1)	{
			$queryString .= '&cHash=' . t3lib_div::calculateCHash($pA);
		}
		
		$pageRecordRow = $this->getDB()->exec_SELECTgetSingleRow('*', 'pages', 'uid=' . $pageID);

		$linkData = $this->getTStemplate()->linkData(
			$pageRecordRow,
			'',
			false,
			'index.php',
			array(),
			$queryString
		);

		$urlObj->encodeSpURL($linkData);

		$tmpQuerystring = explode('&', $queryString);
		foreach ($tmpQuerystring as $tmpQuerystringPart) {
			$tmp = explode('=', $tmpQuerystringPart);
			if ($tmp[0] == 'L')
				$languageUID = $tmp[1];
		}
		t3lib_div::devLog('1.) link Data', 'xmlsitemap_host',0,$linkData);
		$linkData['totalURL'] = $this->getLanguageHostname($languageUID) . '/' . $linkData['totalURL'];
		t3lib_div::devLog('3.) link Data result', 'xmlsitemap_host',0,$linkData);
		return $linkData['totalURL'];
	}
	
	protected function getLanguageHostname($languageUID) {
		
		$domainsCfg = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DOMAINS']['encode'];
		
		foreach ($domainsCfg as $domainCfg) {
			if ($domainCfg['GETvar'] == 'L' && $domainCfg['value'] == $languageUID) {
				t3lib_div::devLog('2.) get lang-hostname', 'xmlsitemap_host',0,array($languageUID,$domainCfg));
				return $domainCfg['urlPrepend'];
			}
		}
		t3lib_div::devLog('2.) get lang-hostname', 'xmlsitemap_host',0,array($languageUID));
		return 'http://' . $GLOBALS['xmlsitemap']['baseURL'] . '/';
	}
	
	public function getTStemplate() {
		if (!$this->TStemplate)
			$this->TStemplate = t3lib_div::makeInstance('t3lib_TStemplate');
			
		return $this->TStemplate;
	}
	
	public function setUrlobj(tx_realurl $urlObj) {
		$this->urlObj = $urlObj;
	}
	
	public function getUrlobj() {
		if (!$this->urlObj)
			$this->urlObj = t3lib_div::makeInstance('tx_realurl');
		
		return $this->urlObj;
	}
}
?>