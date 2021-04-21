<?php

namespace Excellence\Geoip\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;

class GetAllowedCurrencies extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Geoip List action
     *
     * @return void
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $scopeConfigObject = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $currencyConfig = $objectManager->get('Magento\Config\Model\Config\Source\Locale\Currency');
        $currencies = $currencyConfig->toOptionArray();

        $storeId = $this->getRequest()->getParam('storeId');

        $allowedCurrencies = $scopeConfigObject->getValue(
                    'currency/options/allow',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $storeId
                    );

        // Show Allowed Currencies
        $savedCurrencies = explode(',', $allowedCurrencies);
        $allowedStoreCurrencies = array();
        foreach ($currencies as $key=>$option) {
            if (in_array($option['value'], $savedCurrencies)){
                $allowedStoreCurrencies[] = $option;
            }
        }
        
        return $this->resultJsonFactory->create()->setData($allowedStoreCurrencies);
    }
}
