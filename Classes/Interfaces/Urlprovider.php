<?php
/**
 * 
 * Interface for the URL-Providers
 * @author Philipp Bergsmann
 *
 */
interface Tx_Xmlsitemap_Interfaces_Urlprovider
{
	/**
	 * 
	 * Returns a PHP-Array in the schema of the XML
	 * array(array('loc'=>URL, 'lastmod'=>Date/Time),...)
	 */
	public function getURLArray();
	
	/**
	 * 
	 * Returns the name of the current Urlprovider
	 * used for naming the sitemap.xml file
	 */
	public function getName();
}
?>