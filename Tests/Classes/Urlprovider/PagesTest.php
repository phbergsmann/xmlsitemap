<?php
class Tx_Xmlsitemap_Urlprovider_PagesTest extends tx_phpunit_testcase
{
	/**
	 * @test
	 */
	public function geturlsReturnsAnArrayOfUrls() {
		$dbMock = $this->getMock('t3lib_DB');
		$dbMock->expects($this->once())
				->method('exec_SELECTgetRows')
				->will($this->returnValue(array(array('uid'=>1,'tstamp'=>time()))));
		
		$cObjMock = $this->getMock('tslib_cObj');
		$cObjMock->expects($this->once())
				->method('getTypoLink_URL')
				->with(
					$this->equalTo(1)
				)
				->will($this->returnValue('home'));
				
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_Pages');
		$fixture->setDB($dbMock);
		$fixture->setCObject($cObjMock);

		$this->assertType('array',$fixture->getURLArray());
	}
	
	/**
	 * @test
	 */
	public function injectingADatabseIsPossible() {
		$dbMock = $this->getMock('t3lib_DB');
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_Pages');
		$this->assertType('t3lib_DB', $fixture->setDB($dbMock));
	}
	
	/**
	 * @test
	 * @expectedException t3lib_error_Exception
	 */
	public function injectingWrongDatabaseTypeThrowsException() {
		$fixture = t3lib_div::makeInstance('Tx_Xmlsitemap_Urlprovider_Pages');
		$fixture->setDB('keine DB');
	}
}
?>