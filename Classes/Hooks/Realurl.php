<?php
class Tx_Xmlsitemap_Hooks_Realurl
{
	public function getHost(&$hookParams, $obj) {
		if (!$hookParams['host'] && $GLOBALS['xmlsitemap']['baseURL'])
			$hookParams['host'] = $GLOBALS['xmlsitemap']['baseURL'];

		return $hookParams['host'];
	}
}
?>