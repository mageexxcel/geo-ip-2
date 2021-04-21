<?php

namespace Excellence\Geoip\Model\Adminhtml\Config\Source;

class CountryList implements \Magento\Framework\Option\ArrayInterface
{

    protected $_scopeConfigObject;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_scopeConfigObject = $scopeConfigObject;
        $this->_storeManager = $storeManager;
    }

    public function toOptionArray()
    {
        $allStores = $this->_storeManager->getStores();
        $savedCountries = array();
        foreach ($allStores as $_eachStoreId => $val) 
        {
            $_storeCode = $this->_storeManager->getStore($_eachStoreId)->getCode();
            $_storeName = $this->_storeManager->getStore($_eachStoreId)->getName();
            $_storeId = $this->_storeManager->getStore($_eachStoreId)->getId();
            $selected = $this->_scopeConfigObject->getValue(
                    'geoip/advance_setting/selected_countries',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $_storeId
                    );
            $savedCountries = array_merge($savedCountries, explode(',', $selected));
        }
        $savedCountries = array_unique($savedCountries);

        $optionArray = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $country = $objectManager->get('Magento\Directory\Model\Country');
        $optionArray = $country->getResourceCollection()
                               ->loadByStore()
                               ->toOptionArray(false);
        foreach ($optionArray as $key=>$option) {
            if (in_array($option['value'], $savedCountries)){
                $optionArray[$key]['is_region_visible'] = 0;
            }
        }
        return $optionArray;
    }   
}