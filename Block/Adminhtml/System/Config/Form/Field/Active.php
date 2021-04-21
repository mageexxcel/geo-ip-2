<?php
namespace Excellence\Geoip\Block\Adminhtml\System\Config\Form\Field;
class Active extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];
    protected $_AllCountryList;
    protected $_AllCurrencyList;

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Check if columns are defined, set template
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Returns renderer for country element
     * 
     * @return \Magento\Braintree\Block\Adminhtml\Form\Field\Countries
     */
    protected function getAllCountryList() {
        if (!$this->_AllCountryList) {
            $this->_AllCountryList = $this->getLayout()->createBlock(
                    '\Excellence\Geoip\Block\Adminhtml\Form\Field\AllCountry', '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_AllCountryList;
    }
    protected function getAllCurrencyList(){
        if (!$this->_AllCurrencyList) {
            $this->_AllCurrencyList = $this->getLayout()->createBlock(
                    '\Excellence\Geoip\Block\Adminhtml\Form\Field\CurrencyList', '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_AllCurrencyList;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender() {
        $this->addColumn(
            'country',
            [
                'label' => __('Country'),
                'renderer' => $this->getAllCountryList()
            ]
        );
        $this->addColumn(
            'currency', 
            [
                'label' => __('Currency'),
                'renderer' => $this->getAllCurrencyList()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
   
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $country = $row->getCountry();
        $options = [];
        if ($country) {
            $options['option_' . $this->getAllCountryList()->calcOptionHash($country)] = 'multiple="multiple"';
        }
        $row->setData('option_extra_attrs', $options);
        $currency = $row->getCurrency();
        $options = [];
        if($currency){
            $options['option_' . $this->getAllCurrencyList()->calcOptionHash($currency)] = 'multiple="multiple"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}


