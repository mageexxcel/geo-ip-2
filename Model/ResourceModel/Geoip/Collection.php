<?php

/**
 * Geoip Resource Collection
 */
namespace Excellence\Geoip\Model\ResourceModel\Geoip;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Excellence\Geoip\Model\Geoip', 'Excellence\Geoip\Model\ResourceModel\Geoip');
    }
}
