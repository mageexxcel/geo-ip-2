<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Excellence\Geoip\Block\Adminhtml\Form\Field;

class AllCountry extends \Magento\Framework\View\Element\Html\Select {

    /**
     * methodList
     *
     * @var array
     */
    protected $groupfactory;
    protected $countryList;
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
    \Magento\Directory\Model\Country $countryList,
    array $data = []
    ) {
        $this->countryList = $countryList;
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
            
            $countryCollection = $this->countryList->getResourceCollection()
                                                    ->loadData() 
                                                    ->toOptionArray(false);
            foreach ($countryCollection as $country){
                $this->addOption($country['value'], $country['label']);
            }
        }
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '<select name="' .
            $this->getName() .
            '" id="' .
            $this->getId() .
            '" class="' .
            $this->getClass() .
            '" title="' .
            $this->getTitle() .
            '" ' .
            $this->getExtraParams() .
            'multiple="multiple">';

        $values = $this->getValue();
        if (!is_array($values)) {
            $values = (array)$values;
        }

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            $optgroupName = '';
            if ($isArrayOption && is_array($option)) {
                $value = $option['value'];
                $label = (string)$option['label'];
                $optgroupName = isset($option['optgroup-name']) ? $option['optgroup-name'] : $label;
                $params = !empty($option['params']) ? $option['params'] : [];
            } else {
                $value = (string)$key;
                $label = (string)$option;
                $isArrayOption = false;
                $params = [];
            }

            if (is_array($value)) {
                $html .= '<optgroup label="' . $label . '" data-optgroup-name="' . $optgroupName . '">';
                foreach ($value as $keyGroup => $optionGroup) {
                    if (!is_array($optionGroup)) {
                        $optionGroup = ['value' => $keyGroup, 'label' => $optionGroup];
                    }
                    $html .= $this->_optionToHtml($optionGroup, in_array($optionGroup['value'], $values));
                }
                $html .= '</optgroup>';
            } else {
                $html .= $this->_optionToHtml(
                    ['value' => $value, 'label' => $label, 'params' => $params],
                    in_array($value, $values)
                );
            }
        }
        $html .= '</select>';
        
        return $html;
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
