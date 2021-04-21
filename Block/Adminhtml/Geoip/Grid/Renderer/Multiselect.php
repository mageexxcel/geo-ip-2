<?php
namespace Excellence\Geoip\Block\Adminhtml\Geoip\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Multiselect extends AbstractRenderer
{
    private $_storeManager;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context, 
        StoreManagerInterface $storemanager, 
        \Excellence\Geoip\Model\Adminhtml\Config\Source\Country $countryList,
        array $data = []
        )
    {
        $this->_storeManager = $storemanager;
        $this->_countryList = $countryList;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     *
     * @param Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $rowData = $row->getData();
        $countryCodes = explode(',', $rowData['country_codes']);
        $countryArray = $this->_countryList->toOptionArray();
        $countryLabels = '';
        $count = 1;
        foreach ($countryCodes as $countryCode) {
            if(array_key_exists($countryCode, $countryArray)){
                $countryLabels .= $countryArray[$countryCode];
                if($count != count($countryCodes)){
                    $countryLabels .= ", ";
                } 
                $count++;
            }
        }
        return $countryLabels;
    }
}