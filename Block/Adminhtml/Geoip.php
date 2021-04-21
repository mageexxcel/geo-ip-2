<?php
/**
 * Adminhtml geoip list block
 *
 */
namespace Excellence\Geoip\Block\Adminhtml;

class Geoip extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_geoip';
        $this->_blockGroup = 'Excellence_Geoip';
        $this->_headerText = __('GeoIP');
        $this->_addButtonLabel = __('Add New Mapping');
        parent::_construct();
        if ($this->_isAllowedAction('Excellence_Geoip::save')) {
            $this->buttonList->update('add', 'label', __('Add New Mapping'));
        } else {
            $this->buttonList->remove('add');
        }
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
