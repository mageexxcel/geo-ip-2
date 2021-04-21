<?php
namespace Excellence\Geoip\Block\Adminhtml\Geoip;

/**
 * Adminhtml Geoip grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Excellence\Geoip\Model\Geoip
     */
    protected $_geoip;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Excellence\Geoip\Model\Geoip $geoipPage
     * @param \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Excellence\Geoip\Model\Geoip $geoip,
        \Excellence\Geoip\Model\Adminhtml\Config\Source\Storeview $storeList,
        \Excellence\Geoip\Model\Adminhtml\Config\Source\Currency $currencyList,
        \Excellence\Geoip\Model\Adminhtml\Config\Source\Country $countryList,
        \Excellence\Geoip\Model\ResourceModel\Geoip\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_geoip = $geoip;
        $this->_storeList = $storeList;
        $this->_currencyList = $currencyList;
        $this->_countryList = $countryList;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('geoipGrid');
        $this->setDefaultSort('geoip_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        // $this->setFilterVisibility(false);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Excellence\Geoip\Model\ResourceModel\Geoip\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('geoip_id', [
            'header'    => __('ID'),
            'index'     => 'geoip_id',
            'width'     => '50px'
        ]);

        $this->addColumn(
            'store_id',
            [
                'header' => __('Store View'),
                'sortable' => true,
                'index' => 'store_id',
                'type' => 'options',
                'options' => $this->_storeList->toOptionArray(),
                'align' => 'center'
            ]
        );

        $this->addColumn(
            'country_codes',
            array(
                'header' => __('Countries'),
                'index' => 'country_codes',
                'filter' => false,
                'sortable' => false,
                'renderer'  => '\Excellence\Geoip\Block\Adminhtml\Geoip\Grid\Renderer\Multiselect',
            )
        );

        $this->addColumn(
            'currency_code',
            [
                'header' => __('Currency'),
                'sortable' => true,
                'index' => 'currency_code',
                'type' => 'options',
                'options' => $this->_currencyList->toOptionArray(),
                'align' => 'center'
            ]
        );
        
        
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'geoip_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['geoip_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
