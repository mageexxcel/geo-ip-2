<?php

namespace Excellence\Geoip\Controller\Adminhtml\View;

class Mapping extends \Magento\Backend\App\Action
{
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
      $resultPage = $this->resultPageFactory->create();
      $resultPage->getConfig()->getTitle()->prepend(__('GeoIP Mapping'));
      return $resultPage;
    }
}
