<?php
namespace Excellence\Geoip\Plugin\Model;

use Magento\Framework\App\ObjectManager;

class Store
{
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Excellence\Geoip\Helper\Data $geoIpHelper

    ) {
        $this->_storeManager = $storeManager;
        $this->_storeModel = $storeModel;
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_geoIpHelper = $geoIpHelper;
    }

    public function afterGetCurrentCurrencyCode()
    {
        $helper = $this->_geoIpHelper;
        $store = $this->_storeManager->getStore();


        if($helper->toBeSwitched()){
            $code = $helper->getCurrentCurrencyMapping();

            if (!empty($code) && $helper->toBeSwitched()) {
                $allowedCurrencies = explode(',', $this->_scopeConfigObject->getValue('currency/options/allow', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getId()));
                if(count($allowedCurrencies) ) {
                    if (in_array($code, $allowedCurrencies)) {
                        return $code;
                    }
                }
            }
        }
    }
}