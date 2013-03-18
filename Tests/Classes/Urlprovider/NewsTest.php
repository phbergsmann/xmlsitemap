<?php
class Tx_Xmlsitemap_Urlprovider_NewsTest extends tx_phpunit_testcase
{
	/**
	 * @test
	 */
	public function dbIsInjectable() {
		$dbMock = $this->getMock('t3lib_db');
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_News');
		$fixture->setDB($dbMock);
		$this->assertEquals($dbMock, $fixture->getDB());
	}
	
	/**
	 * @test
	 */
	public function getnewsrecordsReturnsRecordArray() {
		$time = time();
		$dbResult = array(array('uid' => 1, 'tstamp' => $time, 'pid' => 1));
		
		$dbMock = $this->getMock('t3lib_db');
		$dbMock->expects($this->once())
				->method('exec_SELECTgetRows')
				->will($this->returnValue($dbResult));
		
		$cObjectMock = $this->getMock('tslib_cObj');
		$cObjectMock->expects($this->once())
					->method('getTypoLink_URL')
					->will($this->returnValue('single_news_record'));

		$pageselectMock = $this->getMock('t3lib_pageSelect');
		$pageselectMock->expects($this->once())
						->method('getRecordOverlay')
						->will($this->returnValue(false));
					
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_News');

		$fixture->setDB($dbMock);
		$fixture->setCObject($cObjectMock);
		$fixture->setPageSelect($pageselectMock);
		$fixture->setBaseURL('http://t3dev.pbergsmann.opendo.at/');
		
		$fixtureDBReturn = $fixture->getNewsRecords();
		
		$this->assertArrayHasKey('uid', $fixtureDBReturn[0]);
		$this->assertArrayHasKey('tstamp', $fixtureDBReturn[0]);
		$this->assertArrayHasKey('loc', $fixtureDBReturn[0]);
		$this->assertArrayHasKey('lastmod', $fixtureDBReturn[0]);
		$this->assertArrayHasKey('pid', $fixtureDBReturn[0]);
		
		$this->assertEquals('http://t3dev.pbergsmann.opendo.at/single_news_record', $fixtureDBReturn[0]['loc']);
		$this->assertEquals(date('c', $time), $fixtureDBReturn[0]['lastmod']);
	}
	
	/**
	 * @test
	 */
	public function baseUrlIsSettable() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_News');
		
		$baseUrl = 'http://phpunit.pbergsmann.opendo.at/';
		
		$fixture->setBaseURL($baseUrl);
		
		$this->assertEquals($baseUrl, $fixture->getBaseURL());
	}
}
?>