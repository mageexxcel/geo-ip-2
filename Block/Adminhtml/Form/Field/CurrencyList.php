<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Excellence\Geoip\Block\Adminhtml\Form\Field;

class CurrencyList extends \Magento\Framework\View\Element\Html\Select {

    /**
     * methodList
     *
     * @var array
     */
    protected $groupfactory;
    protected $currency;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Braintree\Model\System\Config\Source\Country $countrySource
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Context $context, 
    \Magento\Config\Model\Config\Source\Locale\Currency $currency,
    array $data = []
    ) {
        $this->currency = $currency;
        parent::__construct($context, $data);
        
    }

    /**
     * Returns countries array
     * 
     * @return array
     */

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml() {
        if (!$this->getOptions()) {
            
            $currencyCollection = $this->currency->toOptionArray();
            foreach ($currencyCollection as $currency){
                $this->addOption($currency['value'], $currency['label']);
            }
        }
        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     * 
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value);
    }

}
