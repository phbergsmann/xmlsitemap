<?php
/**
 * 
 * Enter description here ...
 * @author Philipp Bergsmann
 *
 */
class Tx_Xmlsitemap_Scheduler_Scheduler extends tx_scheduler_Task
{
	protected $Urlproviders;
	protected $Sitemapindex;
	protected $SitemapClass = 'Tx_Xmlsitemap_Domain_Sitemap';
	protected $SitemapindexClass = 'Tx_Xmlsitemap_Domain_Sitemapindex';
	
	public function execute() {
		$this->create_FE_ENV(0);
		$GLOBALS['xmlsitemap']['baseURL'] = $this->baseURL;
		t3lib_div::devLog('executing scheduler', 'xmlsitemap',0);
		$this->Sitemapindex = t3lib_div::makeInstance($this->SitemapindexClass);
				
		$this->getSelectedURLProviders();
		$this->addSitemaps();
		
		$return = $this->Sitemapindex->writeXMLToFile();

		return true;
	}
	
	/**
	 * 
	 * iterates through the url-providers and adds the sitemaps
	 */
	protected function addSitemaps() {
		foreach ($this->Urlproviders as $Urlprovider) {
			
			$SitemapObject = t3lib_div::makeInstance($this->SitemapClass);
			
			$UrlproviderObject = t3lib_div::makeInstance($Urlprovider);
			
			$SitemapObject->setProvider($UrlproviderObject);
			
			$this->Sitemapindex->addSitemap($SitemapObject);
			
			t3lib_div::devLog('selectedUrlproviders', 'xmlsitemap',0,array($Urlprovider));
		}
	}
	
	/**
	 * 
	 * @return void
	 * @throws Tx_Xmlsitemap_Exceptions_Nourlprovider if no URL-Provider was selected
	 */
	protected  function getSelectedURLProviders() {
		$Urlproviders = unserialize($this->selectedUrlproviders);
		
		if (!$Urlproviders)
			throw new Tx_Xmlsitemap_Exceptions_Nourlprovider('no URL-Provider found', 123);
		
		$Urlproviders = (is_array($Urlproviders)) ? $Urlproviders : array($Urlproviders);
		
		$this->Urlproviders = $Urlproviders;
		
		t3lib_div::devLog('url providers', 'xmlsitemap',0,$this->Urlproviders);
	}
	
	/**
	 * 
	 * inits the $GLOBALS['TSFE']
	 * @param int $pageUid
	 * @param bool $overrule
	 */
	function initTSFE($pageUid = 1, $overrule = FALSE) {
	
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, '');
		
		$GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        //$GLOBALS['TSFE']->checkAlternativeIdMethods();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->getCompressedTCarray();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
        
        TSpagegen::pagegenInit();
       
        echo 'TSFE';
/*		
	        // begin
	    if (!is_object($GLOBALS['TT']) || $overrule === TRUE) {
	        $GLOBALS['TT'] = new t3lib_timeTrack;
	        $GLOBALS['TT']->start();
	    }
	
	    if ((!is_object($GLOBALS['TSFE']) || $overrule === TRUE) && is_int($pageUid)) 
	{
	            // builds TSFE object
			$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageUid);
	            // builds rootline
	        $GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
	        $rootLine = $GLOBALS['TSFE']->sys_page->getRootLine($pageUid);
	
	            // init template
	        $GLOBALS['TSFE']->tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext');
	        $GLOBALS['TSFE']->tmpl->tt_track = 0;// Do not log time-performance information
	        $GLOBALS['TSFE']->tmpl->init();
	
	            // this generates the constants/config + hierarchy info for the template.
	        $GLOBALS['TSFE']->tmpl->runThroughTemplates($rootLine, $start_template_uid=0);
	        $GLOBALS['TSFE']->tmpl->generateConfig();
	        $GLOBALS['TSFE']->tmpl->loaded=1;
	
	            // get config array and other init from pagegen
	        $GLOBALS['TSFE']->getConfigArray();
	        $GLOBALS['TSFE']->linkVars = ''.$GLOBALS['TSFE']->config['config']['linkVars'];
	
	        if ($GLOBALS['TSFE']->config['config']['simulateStaticDocuments_pEnc_onlyP']) 
	{
	            foreach (t3lib_div::trimExplode(',',$GLOBALS['TSFE']->config['config']['simulateStaticDocuments_pEnc_onlyP'],1) as $temp_p) {
	                $GLOBALS['TSFE']->pEncAllowedParamNames[$temp_p]=1;
	            }
	        }
	            // builds a cObj
	        $GLOBALS['TSFE']->newCObj();
	    }
*/
	}
	
	private function create_FE_ENV($id){

        global $TYPO3_CONF_VARS;
 
        require_once(PATH_tslib.'class.tslib_fe.php');
 
        if(!is_object($GLOBALS['TSFE'])){
 
            require_once(PATH_t3lib.'class.t3lib_timetrack.php');
            if(!is_object($GLOBALS['TT'])){
                $GLOBALS['TT'] = new t3lib_timeTrack;
                $GLOBALS['TT']->start();
            }
 
            require_once(PATH_t3lib.'class.t3lib_page.php');
            require_once(PATH_t3lib.'class.t3lib_userauth.php');
            require_once(PATH_tslib.'class.tslib_feuserauth.php');
            require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
            require_once(PATH_t3lib.'class.t3lib_cs.php');
            require_once(PATH_tslib.'class.tslib_content.php');
            // require_once(PATH_tslib.'class.tslib_menu.php');
 
            $temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
            $GLOBALS['TSFE'] = new $temp_TSFEclassName(    $TYPO3_CONF_VARS,    $id, 0,    '',    '',    '',    '',    '');
        }

        $GLOBALS['TSFE']->no_cache = true;
        $GLOBALS['TSFE']->id=$id;
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->fetch_the_id();        
        // Look up the page
        $GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
        $GLOBALS['TSFE']->sys_page->init($GLOBALS['TSFE']->showHiddenPage);
        
        // If the page is not found (if the page is a sysfolder, etc), then return no URL, preventing any further processing which would result in an error page.
        // $page = $GLOBALS['TSFE']->sys_page->getPage($id);
        
        
        $GLOBALS['TSFE']->getPageAndRootline();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->forceTemplateParsing = 1;
        
        $GLOBALS['TSFE']->getConfigArray();    
        
        // Find the root template
        $GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);

        /*
        // cObj    
        $this->cObj = t3lib_div::makeInstance('tslib_cObj');
        $this->cObj->start(array(),'');
        */
    }
    
    public function getAdditionalInformation() {
    	$urlProviders = unserialize($this->selectedUrlproviders);
    	foreach ($urlProviders as $urlProvider) {
    		foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['xmlsitemap']['urlprovider'] as $availableProvider) {
    			if ($availableProvider['classname'] == $urlProvider) {
    				$selected[] = $availableProvider['name'];
    			}
    		}
    	}
    	return implode(', ', $selected) . ' - ' . $this->baseURL;
    }
}
?>