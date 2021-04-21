<?php

namespace Excellence\Geoip\Model\Adminhtml\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $country = $objectManager->get('Magento\Directory\Model\Country');
        $countries = $country->getResourceCollection()
                               ->loadByStore()
                               ->toOptionArray(false);

        $optionArray = array();
        foreach ($countries as $option) {
            $optionArray[$option['value']] = $option['label'];
        }

        return $optionArray;
    }   
}