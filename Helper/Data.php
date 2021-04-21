<?php
/**
 * Copyright © 2015 Excellence . All rights reserved.
 */
namespace Excellence\Geoip\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const LOCAL_IP = '127.0.0.1';
	const DEFAULT_COUNTRY_CODE = 'US';
	const FIRST_VISIT_COOKIE_NAME = 'is_first_visit';
  	const FIRST_VISIT_COOKIE_DURATION = 86400; // lifetime in seconds
	
	protected $_remoteAddress;
	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Session $catalogSession,
	    \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
	    \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
	    \Magento\Framework\App\Request\Http $request,
	    \Excellence\Geoip\Model\GeoipFactory $geoipFactory,
	    \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory $collectionFactory,
		\Magento\Framework\Module\Dir\Reader $dirReader,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
		\Magento\Framework\App\Helper\Context $context
	) {
		$this->_storeManager = $storeManager;
    	$this->_scopeConfigObject = $context->getScopeConfig();
		$this->_catalogSession = $catalogSession;
	    $this->_cookieManager = $cookieManager;
	    $this->_cookieMetadataFactory = $cookieMetadataFactory;
	    $this->_urlInterface = $context->getUrlBuilder();
	    $this->request = $request;
	    $this->_geoipFactory = $geoipFactory;
	    $this->_collectionFactory = $collectionFactory;
		$this->_dirReader = $dirReader;
		$this->_remoteAddress = $remoteAddress;
		parent::__construct($context);
	}
	public function getIP(){
		$userIP = $this->_remoteAddress->getRemoteAddress();
		
		if(empty($userIP)) {
			$userIP = self::LOCAL_IP;
		}
		return $userIP;
	}
	public function setCookie($name, $value, $duration)
	{
		$metadata = $this->_cookieMetadataFactory
					->createPublicCookieMetadata()
					->setDuration($duration);

		$this->_cookieManager->setPublicCookie(
			$name,
			$value,
			$metadata
		);
	}
	public function readCookie($name)
	{
		$cookieValue = $this->_cookieManager->getCookie($name);
		return $cookieValue;
	}
	public function deleteCookie($name)
	{
		$this->_cookieManager->deleteCookie(
			$name
		);
	}
	public function isHomePage()
	{
		if ($this->request->getFullActionName() == 'cms_index_index')
		{
			return true;
		}
		return false;
	}
	public function isAcceptedUrl()
	{
		$specifiedUrls = explode(PHP_EOL, $this->_scopeConfigObject->getValue('geoip/advance_settings/accepted_urls'));
		foreach ($specifiedUrls as $specifiedUrl) {
			$currentUrl = $this->getCurrentUrl();
			if(strpos($currentUrl, '?___from_store=') === true){
				$urlExplode = explode('?___from_store=', $currentUrl);
				$currentUrl = $urlExplode[0];
			}
			if(strpos($currentUrl, '?___store=') === true){
				$urlExplode = explode('?___store=', $currentUrl);
				$currentUrl = $urlExplode[0];
			}
			if($currentUrl == trim($specifiedUrl)) {
				return true;
			}
		}
		return false;
	}
	public function isExceptedUrl()
	{
		$specifiedUrls = explode(PHP_EOL, $this->_scopeConfigObject->getValue('geoip/advance_settings/excepted_urls'));
		foreach ($specifiedUrls as $specifiedUrl) {
			$currentUrl = $this->getCurrentUrl();
			if(strpos($currentUrl, '?___from_store=') === true){
				$urlExplode = explode('?___from_store=', $currentUrl);
				$currentUrl = $urlExplode[0];
			}
			if(strpos($currentUrl, '?___store=') === true){
				$urlExplode = explode('?___store=', $currentUrl);
				$currentUrl = $urlExplode[0];
			}
			if($currentUrl == trim($specifiedUrl)){
				return true;
			}
		}
		return false;
	}
	public function getCurrentUrl() {
		return $this->_storeManager->getStore()->getCurrentUrl();
	}
	public function isRestrictedIp()
	{
		$restrictedIps = explode(PHP_EOL, $this->_scopeConfigObject->getValue('geoip/advance_settings/restricted_ips'));
		foreach ($restrictedIps as $restrictedIp) {
			if($this->getIP() == $restrictedIp){
				return true;
			}
		}
		return false;
	}

	public function getAllowedCurrencies()
	{
		return $this->_scopeConfigObject->getValue('currency/options/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getStoreId());
	}

	public function getCurrentCurrencyMapping()
	{
		$allowedCurrencies =  explode(',', $this->getAllowedCurrencies());

		$storeId = $this->_storeManager->getStore()->getStoreId();
		$currency_code = null;
		$countryCode = $this->getCountryCode();

		$model = $this->_collectionFactory->create();

		$currentModel = $model;


		$filteredCollection = $currentModel
						->addFieldToFilter('country_codes', array('like' => '%'.$countryCode.'%'))
						->addFieldToFilter('store_id', $storeId)->getFirstItem();


		if(count($filteredCollection->getData()) > 0){
			$currency_code = $filteredCollection->getData('currency_code');
		} else{
			$defaultStoreModel = $this->_collectionFactory->create();
			$defaultStoreData = $defaultStoreModel
						->addFieldToFilter('country_codes', array('like' => '%'.$countryCode.'%'))
						->addFieldToFilter('store_id', 0)->getFirstItem();
			$currency_code = $defaultStoreData->getData('currency_code');
		}

		if(in_array($currency_code, $allowedCurrencies)){
			return $currency_code;
		}
	}
	public function getCountryCode()
	{
		$country_code = null;
		$userIP = $this->getIP();
		if (array_key_exists('HTTP_CF_IPCOUNTRY', $_SERVER)) {
	      	// If CloudFlare is Installed
	      	$country_code = $_SERVER["HTTP_CF_IPCOUNTRY"];
	    } else {
	    	$moduleDir = $this->_dirReader->getModuleDir('', "Excellence_Geoip");
			// If CloudFlare is not Installed
			include_once $moduleDir."/lib/geoip.inc";
			$giObject = geoip_open($moduleDir."/lib/GeoIP.dat", GEOIP_STANDARD);
			$country_code = geoip_country_code_by_addr($giObject, $userIP);
		}
		if (empty($country_code)) {
			$country_code = self::DEFAULT_COUNTRY_CODE;
		}
	    return $country_code;
	}

	public function toBeSwitched()
	{
		// Is Module Enabled?
	    if (!($this->_scopeConfigObject->getValue('geoip/basic_setting/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getStoreId()))) {
	        return false;
	    }

	    // Check First Visit?
	    if (!($this->_scopeConfigObject->getValue('geoip/advance_settings/geoip_first_visit')) && !empty($this->readCookie(self::FIRST_VISIT_COOKIE_NAME))) {
	        return false;
	    }

	    // Check Redirect Rule
	    switch ($this->_scopeConfigObject->getValue('geoip/advance_settings/apply_redirect')) {
	        case 1:
	            #redirection for home page only
	            if (!($this->isHomePage())) {
	                return false;
	            }
	            break;

	        case 2:
	            #redirection for specified url
	            if (!($this->isAcceptedUrl())) {
	                return false;
	            }
	            break;

	        case 3:
	            #block redirection for excepted url
	            if ($this->isExceptedUrl()) {
	                return false;
	            }
	            break;

	        default:
	            #code...
	            break;
	    }

	    // Check for restricted IPs
	    if ($this->isRestrictedIp()) {
	        return false;
	    }
	    return true;
	}
}