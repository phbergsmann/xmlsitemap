<?php
class Tx_Xmlsitemap_Domain_Sitemap {
	
	protected $Urlprovider;
	protected $UrlArray;
	protected $DOMDocument;
	
	/**
	 * 
	 * @param Tx_Xmlsitemap_Interfaces_Urlprovider $urlProvider
	 */	
	public function setProvider(Tx_Xmlsitemap_Interfaces_Urlprovider $urlProvider) {
		$this->Urlprovider = $urlProvider;
		t3lib_div::devLog('added urlProvider', 'xmlsitemap',0,array($urlProvider));
	}
	
	public function getProvider() {
		return $this->Urlprovider;
	}
	
	public function getURLArray() {
		$this->UrlArray = $this->Urlprovider->getURLArray(); 
		return $this->UrlArray;
	}
	
	public function writeToTempFile() {
		$filename = 'typo3temp/tx_xmlsitemap/sitemap_' . $this->Urlprovider->getName() . '.xml';
		
		t3lib_div::writeFileToTypo3tempDir(PATH_site . $filename, $this->getDOMDocument()->saveXML());
		
		return $filename;
	}
	
	public function getURLSet() {
		$this->getURLArray();
		$urlset = $this->getDOMDocument()->createElement('urlset');
		$urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
		foreach ($this->UrlArray as $Url) {
			if ($UrlXML = $this->getUrlXML($Url))
				$urlset->appendChild($UrlXML);
		}
		
		
		$this->getDOMDocument()->appendChild($urlset);
		
		return $urlset;
	}
	
	public function getUrlXML(array $Url) {
		if (!$Url['loc'])
			return false;
			
		$url = $this->getDOMDocument()->createElement('url');
		$loc = $this->getDOMDocument()->createElement('loc', urlencode(htmlspecialchars($Url['loc'],ENT_NOQUOTES,'UTF-8')));
		$lastmod = $this->getDOMDocument()->createElement('lastmod', $Url['lastmod']);
		
		$url->appendChild($loc);
		$url->appendChild($lastmod);
		
		return $url;
	}
	
	public function getDOMDocument() {
		if (is_null($this->DOMDocument))
			$this->DOMDocument = t3lib_div::makeInstance('DOMDocument','1.0','UTF-8');
		
			return $this->DOMDocument;
	}
	
	public function setDOMDocument(DOMDocument $DOMDocument) {
		$this->DOMDocument = $DOMDocument;
	}
}
?>