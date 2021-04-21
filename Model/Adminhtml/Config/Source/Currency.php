<?php

namespace Excellence\Geoip\Model\Adminhtml\Config\Source;
 
class Currency implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        /** @var \Magento\Framework\App\ObjectManager $objectManager */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Store\Model\StoreManagerInterface|\Magento\Store\Model\StoreManager $storeManager */
        $currencyConfig = $objectManager->get('Magento\Config\Model\Config\Source\Locale\Currency');
        $currencies = $currencyConfig->toOptionArray();
        
        $optionArray = array();
        foreach ($currencies as $option) {
            $optionArray[$option['value']] = $option['label'];
        }

        return $optionArray;

    }
}