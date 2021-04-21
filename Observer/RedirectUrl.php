<?php

namespace Excellence\Geoip\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Controller\ResultFactory;

class RedirectUrl implements ObserverInterface {

  const FIRST_VISIT_COOKIE_NAME = 'is_first_visit';
  const FIRST_VISIT_COOKIE_DURATION = 86400; // lifetime in seconds

  public function __construct(
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
    \Magento\Framework\Controller\ResultFactory $result,
    \Magento\Framework\Registry $registry,
    \Excellence\Geoip\Helper\Data $helper,
    \Magento\Framework\App\Request\Http $request
  ) {
    $this->_storeManager = $storeManager;
    $this->_scopeConfigObject = $scopeConfigObject;
    $this->resultFactory = $result;
    $this->registry = $registry;
    $this->_helper = $helper;
    $this->request = $request;
  }


  public function execute(Observer $observer) {

    // Set cookie on first visit
    $this->_helper->setCookie(self::FIRST_VISIT_COOKIE_NAME, 'yes', self::FIRST_VISIT_COOKIE_DURATION);

    if(!$this->_helper->toBeSwitched()){
      return;
    }

    // Process redirect
    $currentStoreId = $this->_storeManager->getStore()->getId();
    $currentWebsiteId = $this->_storeManager->getWebsite()->getId();
    
    $country_code = $this->_helper->getCountryCode();

    $allStores = $this->_storeManager->getStores();
    $savedCountries = array();
    $storeToBeLoaded = $this->_storeManager->getStore()->getStoreId();
    foreach ($allStores as $_eachStoreId => $val) 
    {
        $_storeId = $this->_storeManager->getStore($_eachStoreId)->getId();
        $selected = $this->_scopeConfigObject->getValue(
                'geoip/country_mapping/selected_countries',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $_storeId
                );
        if(strlen($selected) > 0){
        $list = explode(',', $selected);
        
          foreach ($list as $value) {
            $savedCountries[] = array('store_id' => $_storeId, 'countryCode' => $value);
          }
          foreach ($savedCountries as $store_country) {
            if($country_code == $store_country['countryCode']){
              $storeToBeLoaded = $store_country['store_id'];
              break;
            }
          }
        }
    }
    if ($currentStoreId == $storeToBeLoaded) {
      return;
    }
    // Redirect to the desired URL based on the store code which is to be loaded
    return $observer->getControllerAction()->getResponse()->setRedirect($this->_storeManager->getStore($storeToBeLoaded)->getCurrentUrl());
  }

}
