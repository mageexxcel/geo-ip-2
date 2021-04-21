<?php

namespace Excellence\Geoip\Model\ResourceModel;

/**
 * Geoip Resource Model
 */
class Geoip extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('excellence_geoip', 'geoip_id');
    }
    
}
