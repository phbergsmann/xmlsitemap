<?php
class Tx_Xmlsitemap_Domain_Sitemapindex {
	
	protected $sitemapArray;
	protected $DOMDocument = NULL;
	
	/**
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->sitemapArray = array();
	}
	
	/**
	 * 
	 * @param Tx_Xmlsitemap_Domain_Sitemap $sitemap
	 * @return bool
	 */
	public function addSitemap(Tx_Xmlsitemap_Domain_Sitemap $sitemap) {
		$this->sitemapArray[] = $sitemap;
		return true;
	}
	
	/**
	 * adds the sitemaps to the sitemap-index
	 * @return DOMDocument
	 */
	public function getXML() {
		$XML = $this->getDOMDocument();
		
		$sitemapindex = $XML->createElement('sitemapindex');
		$sitemapindex->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
		
		
		
		foreach ($this->sitemapArray as $sitemap) {
			$sitemap->getURLSet();
			$sitemapUrl = $sitemap->writeToTempFile();
			$sitemap = $XML->createElement('sitemap');
			$loc = $XML->createElement('loc', $GLOBALS['TSFE']->tmpl->setup['config.']['baseURL'] . $sitemapUrl);
			$lastmod = $XML->createElement('lastmod', date('c'));
			$sitemap->appendChild($loc);
			$sitemap->appendChild($lastmod);
			$sitemapindex->appendChild($sitemap);
		}
		
		$XML->appendChild($sitemapindex);
		
		return $XML;
	}
	
	/**
	 * 
	 * set the used dom-document
	 * @param DOMDocument $dom
	 */
	public function setDOMDocument(DOMDocument $dom) {
		$this->DOMDocument = $dom;
	}
	
	/**
	 * returns the dom-document
	 * @return DOMDocument
	 */
	public function getDOMDocument() {
		if (is_null($this->DOMDocument)) {
			$this->DOMDocument = t3lib_div::makeInstance('DOMDocument','1.0','UTF-8');
		}
		
		return $this->DOMDocument;
	}
	
	/**
	 * write the xml-file to the filesystem
	 * @return bool
	 */
	public function writeXMLToFile() {
		$XML = $this->getXML();
		$writeSuccess = t3lib_div::writeFileToTypo3tempDir(PATH_site . 'typo3temp/tx_xmlsitemap/sitemap.xml', $XML->saveXML());
		t3lib_div::devLog('writing sitemap to ' . PATH_site . 'typo3temp/tx_xmlsitemap/sitemap.xml','xmlsitemap',0,array($writeSuccess));
		
		return true;
	}
}
?>