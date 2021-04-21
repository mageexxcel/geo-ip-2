<?php

namespace Excellence\Geoip\Block\Adminhtml\View;

class Mapping extends \Magento\Backend\Block\Widget
{
    
    /**
     * @var string
     */
    protected $_template = 'mapping.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Excellence\Geoip\Model\GeoipFactory $geoipFactory,
        array $data = []
        )
    {
        parent::__construct($context, $data);
        $this->_geoipFactory = $geoipFactory;
        $this->setUseContainer(true);
    }

    public function getCountryTableData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $scopeConfigObject = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $optionArray = array();
        $html = '';
        $country = $objectManager->get('Magento\Directory\Model\Country');
        $optionArray = $country->getResourceCollection()
                               ->loadByStore()
                               ->toOptionArray(false);

        $currencyConfig = $objectManager->get('Magento\Config\Model\Config\Source\Locale\Currency');
        $currencies = $currencyConfig->toOptionArray();

        $stores = $storeManager->getStores($withDefault = false);
        $savedCountries = array();
        foreach ($stores as $_eachStoreId => $val) 
        {
            $_storeCode = $storeManager->getStore($_eachStoreId)->getCode();
            $_storeName = $storeManager->getStore($_eachStoreId)->getName();
            $_storeId = $storeManager->getStore($_eachStoreId)->getId();
            $selected = $scopeConfigObject->getValue(
                    'geoip/country_mapping/selected_countries',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $_storeId
                    );
            $allowedCurrencies = $scopeConfigObject->getValue(
                    'currency/options/allow',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $_storeId
                    );
            // Show Mapped Countries
            $savedCountries = explode(',', $selected);
            $selectedStoreCountry = array();
            foreach ($optionArray as $key=>$option) {
                if (in_array($option['value'], $savedCountries)){
                    $selectedStoreCountry[] = $option['label'];
                }
            }

            // Show Allowed Currencies
            $savedCurrencies = explode(',', $allowedCurrencies);
            $allowedStoreCurrencies = array();
            foreach ($currencies as $key=>$option) {
                if (in_array($option['value'], $savedCurrencies)){
                    $allowedStoreCurrencies[] = $option['label'];
                }
            }

            $html .= "<tr>
                        <td>".$_storeName."</td>";

            if(count($selectedStoreCountry) > 0){
                $subHtml = implode(', ', $selectedStoreCountry);

                $html .= "<td>".$subHtml."</td>";
            }
            else{
                $html .= "<td>".__('No Country is Mapped.')."</td>";
            }

            if(count($allowedStoreCurrencies) > 0){
                $subHtml = implode(', ', $allowedStoreCurrencies);

                $html .= "<td>".$subHtml."</td>";
            }
            else{
                $html .= "<td>".__('No Specific Allowed Currencies are Selected')."</td>";
            }
            $html .= "</tr>";
                
        }
        if(strlen($html) == 0){
            $html .= "<tr>
                          <td colspan=3>".__('No Mapping Done.')."</td>
                        </tr>";
        }

        return $html;
    }
    public function getCurrrencyMappingTableData(){
        $model = $this->_geoipFactory->create();
        $data = $model->getCollection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $html = '';
        $country = $objectManager->get('Magento\Directory\Model\Country');
        $countries = $country->getResourceCollection()
                               ->loadByStore()
                               ->toOptionArray(false);

        $currencyConfig = $objectManager->get('Magento\Config\Model\Config\Source\Locale\Currency');
        $currencies = $currencyConfig->toOptionArray();

        if(count($data->getData()) == 0){
            return "<tr>
                        <td colspan=3>".__('No Currency Mapping Done.')."</td>
                    </tr>";
        }
        
        foreach ($data as $value) {
            $html .= "<tr>";
            $rowData = $value->getData();
            if($rowData['store_id'] == 0){
                $html .= "<td>".__('All Store Views')."</td>";
                
            }
            else{
                $html .= "<td>".$storeManager->getStore($rowData['store_id'])->getName()."</td>";
            }

            $savedCountries = explode(',', $rowData['country_codes']);
            $selectedStoreCountry = array();
            foreach ($countries as $key=>$option) {
                if (in_array($option['value'], $savedCountries)){
                    $selectedStoreCountry[] = $option['label'];
                }
            }
            $subHtml = implode(', ', $selectedStoreCountry);

            $html .= "<td>".$subHtml."</td>";

            foreach ($currencies as $currency) {
                if($currency['value'] == $rowData['currency_code']){
                    $html .= "<td>".$currency['label']."</td>";
                    break;
                }
            }
            $html .= "</tr>";
        }
        
        return $html;
    }
}