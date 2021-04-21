<?php

namespace Excellence\Geoip\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class SelectedCountries extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_moduleList;
    protected $_moduleManager;
    protected $_productMetadata;
    protected $_serverAddress;
    protected $_storeManager;
    protected $_cacheManager;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress,
        \Magento\Framework\App\Cache\Proxy $cacheManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_moduleList       = $moduleList;
        $this->_moduleManager    = $moduleManager;
        $this->_storeManager     = $storeManager;
        $this->_productMetadata  = $productMetadata;
        $this->_serverAddress    = $serverAddress;
        $this->_cacheManager    = $cacheManager;
        $this->_objectManager    = $objectManager;
        $this->_request = $context->getRequest();
        $this->_scopeConfigObject = $context->getScopeConfig();
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->getModuleInfoHtml();
    }

    public function getWikiLink()
    {
        return $this->_wikiLink;
    }

    public function getModuleTitle()
    {
        return $this->_moduleName;
    }

    public function getModuleInfoHtml()
    {
        $optionArray = array();
        $html = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $country = $objectManager->get('Magento\Directory\Model\Country');
        $optionArray = $country->getResourceCollection()
                               ->loadByStore()
                               ->toOptionArray(false);

        $allStores = $this->_storeManager->getStores();
        $savedCountries = array();
        foreach ($allStores as $_eachStoreId => $val) 
        {
            $_storeCode = $this->_storeManager->getStore($_eachStoreId)->getCode();
            $_storeName = $this->_storeManager->getStore($_eachStoreId)->getName();
            $_storeId = $this->_storeManager->getStore($_eachStoreId)->getId();
            $selected = $this->_scopeConfigObject->getValue(
                    'geoip/country_mapping/selected_countries',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $_storeId
                    );
            $savedCountries = explode(',', $selected);
            $selectedStoreCountry = array();
            foreach ($optionArray as $key=>$option) {
            if (in_array($option['value'], $savedCountries)){
                    $selectedStoreCountry[] = $option['label'];
                }
            }
            if(count($selectedStoreCountry) > 0){
                $subHtml = implode(', ', $selectedStoreCountry);

                $html .= "<tr>
                              <td>".$_storeName."</td>
                              <td>".$subHtml."</td>
                            </tr>";
            }
                
        }
        if(strlen($html) == 0){
            return;
        }

        $tableHtml = "<table>
                      <thead>
                        <tr>
                          <th>Store View</th>
                          <th>".__('Selected Countries')."</th>
                        </tr>
                      </thead>
                      <tbody>"
                      .$html.
                      "</tbody>
                    </table>";
        
        $m = $this->_moduleList->getOne($this->getModuleName());
        if(!empty($this->_request->getParam('store'))){
            $finalHtml = '<tr><td class="label" colspan="4" style="text-align: left;"><div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            <h2>'.__('Previously Selected Countries:').'</h2>
            ' 
            .$tableHtml.
            '<p style="margin: 20px 0 5px 0;color: #FF0000;"><span style="margin-right: 5px;">&#10148;</span>'.__('Please select the countries which are not selected in other store views').'</p>'.
            '</div></td></tr>';
        }
        else{
            $finalHtml = '<tr><td class="label" colspan="4" style="text-align: left;"><div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            <h2>'.__('Previously Selected Countries:').'</h2>
            ' 
            .$tableHtml.
            '</div></td></tr>';
        }

        return $finalHtml;
    }

}