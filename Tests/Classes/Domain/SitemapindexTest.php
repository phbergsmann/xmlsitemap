<?php
class Tx_Xmlsitemap_Domain_SitemapindexTest extends tx_phpunit_testcase
{
	/**
	 * 
	 * @test
	 */
	public function addingASitemapIsPossible() {
		$sitemap = $this->getMock('Tx_Xmlsitemap_Domain_Sitemap');
		
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemapindex');
		
		$this->assertTrue($fixture->addSitemap($sitemap));
	}
	
	/**
	 * 
	 * @test
	 * @expectedException t3lib_error_Exception
	 */
	public function addingASitemapWithWrongTypeThrowsException() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemapindex');
		
		$fixture->addSitemap('xxx');
	}
	
	/**
	 * @test
	 */
	public function exportingAXmlIsPossible() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemapindex');
		
		$this->assertType('DOMDocument',$fixture->getXML());
	}
	
	/**
	 * @test
	 */
	public function xmlfileIsWrittenToFilesystem() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemapindex');
		
		$this->assertTrue($fixture->writeXMLToFile());
	}
	
	/**
	 * @test
	 */
	public function settingTheDomdocumentIsPossible() {
		$domMock = $this->getMock('DOMDocument');
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Domain_Sitemapindex');
		
		$fixture->setDOMDocument($domMock);
		$this->assertType('DOMDocument', $fixture->getDOMDocument());
	}
}
?>