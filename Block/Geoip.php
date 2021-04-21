<?php

namespace Excellence\Geoip\Block;

/**
 * Geoip content block
 */
class Geoip extends \Magento\Framework\View\Element\Template
{
    /**
     * Geoip collection
     *
     * @var Excellence\Geoip\Model\ResourceModel\Geoip\Collection
     */
    protected $_geoipCollection = null;
    
    /**
     * Geoip factory
     *
     * @var \Excellence\Geoip\Model\GeoipFactory
     */
    protected $_geoipCollectionFactory;
    
    /** @var \Excellence\Geoip\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory $geoipCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory $geoipCollectionFactory,
        \Excellence\Geoip\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_geoipCollectionFactory = $geoipCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve geoip collection
     *
     * @return Excellence\Geoip\Model\ResourceModel\Geoip\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_geoipCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared geoip collection
     *
     * @return Excellence\Geoip\Model\ResourceModel\Geoip\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_geoipCollection)) {
            $this->_geoipCollection = $this->_getCollection();
            $this->_geoipCollection->setCurPage($this->getCurrentPage());
            $this->_geoipCollection->setPageSize($this->_dataHelper->getGeoipPerPage());
            $this->_geoipCollection->setOrder('published_at','asc');
        }

        return $this->_geoipCollection;
    }
    
    /**
     * Fetch the current page for the geoip list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Excellence\Geoip\Model\Geoip $geoipItem
     * @return string
     */
    public function getItemUrl($geoipItem)
    {
        return $this->getUrl('*/*/view', array('id' => $geoipItem->getId()));
    }
    
    /**
     * Return URL for resized Geoip Item image
     *
     * @param Excellence\Geoip\Model\Geoip $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('geoip_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $geoipPerPage = $this->_dataHelper->getGeoipPerPage();

            $pager->setAvailableLimit([$geoipPerPage => $geoipPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
}
