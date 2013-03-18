<?php
class Tx_Xmlsitemap_Domain_SitemapTest extends tx_phpunit_testcase
{
	/**
	 * 
	 * @test
	 */
	public function creatingASitemapobjectIsPossible() {
		$urlProvider = $this->getMock('Tx_Xmlsitemap_Urlprovider_Pages');
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$fixture->setProvider($urlProvider);
	}
	
	/**
	 * @test
	 */
	public function gettingTheUrlproviderIsPossible() {
		$urlProvider = $this->getMock('Tx_Xmlsitemap_Urlprovider_Pages');
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$fixture->setProvider($urlProvider);
		$this->assertInstanceOf('Tx_Xmlsitemap_Urlprovider_Pages', $fixture->getProvider());
	}
	
	/**
	 * @test
	 */
	public function geturlarrayReturnsAnArray() {
		$urlProvider = $this->getMock('Tx_Xmlsitemap_Urlprovider_Pages');
		$urlProvider->expects($this->once())
					->method('getURLArray')
					->will($this->returnValue(array()));
					
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$fixture->setProvider($urlProvider);
		$this->assertInternalType('array', $fixture->getURLArray());
	}
	
	/**
	 * @test
	 */
	public function geturlxmlReturnsFalseIfNoUrlIsGenerated() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$this->assertFalse($fixture->getUrlXML(array('loc'=>'', 'lastmod'=>'2011-03-29T18:23:43+02:00')));
	}
	
	/**
	 * @test
	 */
	public function geturlxmlReturnsADomelement() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$this->assertInstanceOf('DOMElement', $fixture->getUrlXML(array('loc'=>'index.php?id=2', 'lastmod'=>'2011-03-29T18:23:43+02:00')));
	}
	
	/**
	 * @test
	 */
	public function theDomobjectIsSettable() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		
		$domMock = $this->getMock('DOMDocument');
		
		$fixture->setDOMDocument($domMock);
		
		$this->assertEquals($domMock, $fixture->getDOMDocument());
	}
	
	/**
	 * @test
	 */
	public function writeToTempFileWorks() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemap');
		$domMock = $this->getMock('DOMDocument');
		$domMock->expects($this->once())
				->method('saveXML')
				->will($this->returnValue('test'));
				
		$urlproviderMock = $this->getMock('Tx_Xmlsitemap_Urlprovider_Pages');
		$urlproviderMock->expects($this->once())
						->method('getName')
						->will($this->returnValue('phpunit'));
						
		$fixture->setDOMDocument($domMock);
		$fixture->setProvider($urlproviderMock);
		
		$fixture->writeToTempFile();
		
		$this->assertFileExists(PATH_site . 'typo3temp/tx_xmlsitemap/sitemap_phpunit.xml');
		
		unlink(PATH_site . 'typo3temp/tx_xmlsitemap/sitemap_phpunit.xml');
	}
}
?>