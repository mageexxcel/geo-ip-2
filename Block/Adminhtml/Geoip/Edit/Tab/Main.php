<?php
namespace Excellence\Geoip\Block\Adminhtml\Geoip\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Excellence\Geoip\Model\Adminhtml\Config\Source\CountryList $countryList,
        \Magento\Config\Model\Config\Source\Locale\Currency $currencyList,
        \Magento\Backend\Helper\Data $helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_countryList = $countryList;
        $this->_currencyList = $currencyList;
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('geoip');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Excellence_Geoip::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('geoip_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Mapping Information')]);

        if ($model->getId()) {
            $fieldset->addField('geoip_id', 'hidden', ['name' => 'geoip_id']);
        }

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'store_id[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'country_codes',
            'multiselect',
            [
                'name' => 'country_codes',
                'label' => __('Select Countries'),
                'title' => __('Select Countries'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $this->_countryList->toOptionArray(),
            ]
        );

        $script = "<script>
                    require([
                        'jquery'
                      ], function (jQuery) {
                        var showAllowedCurrencies =  function(value){
                            jQuery.ajax( {
                                url: '".$this->_helper->getUrl('geoip/*/getallowedcurrencies')."',
                                data: {form_key: window.FORM_KEY, storeId: value},
                                dataType: 'json',
                                type: 'POST',
                                showLoader: true
                            }).done(function(data) {
                                var optionHtml = '';
                                var len = data.length;
                                if(len > 0){
                                    jQuery('#loading-msg-product').remove();
                                    for(var i = 0; i < len; i++){
                                        optionHtml += '<option value='+ data[i]['value'] +'>'+data[i]['label']+'</option>';
                                    }

                                    jQuery('#geoip_main_currency_code').html(optionHtml);
                                }
                                if(jQuery('#geoip_main_currency_code').attr('disabled')){
                                    jQuery('#geoip_main_currency_code').removeAttr('disabled');
                                }                        
                            });
                        }
                        
                        jQuery('#geoip_main_currency_code').ready(function(){
                            showAllowedCurrencies(jQuery('#geoip_main_store_id').val());
                        });

                        jQuery(document).on('change', '#geoip_main_store_id', function(){
                            showAllowedCurrencies(jQuery(this).val());
                        });

                        
                    });
                    </script>";

        $comment = "<font size=2 color='#666666'>".__("Refer ")."<a href='".$this->_helper->getUrl('geoip/view/mapping')."' target = '_blank'>".__("this guide")."</a>".__(' to read the instructions.')."</font></p>";

        $fieldset->addField(
            'currency_code',
            'select',
            [
                'name' => 'currency_code',
                'label' => __('Currency'),
                'title' => __('Currency'),
                'class' => 'required-entry',
                'required' => true,
                'values' => [['value' => '', 'label' => __('Please Wait')]],
                'disabled' => true,
                'after_element_html' => $comment,
                'before_element_html' => $script
            ]
        );

        
        $this->_eventManager->dispatch('adminhtml_geoip_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Mapping Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Mapping Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
