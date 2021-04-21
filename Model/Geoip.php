<?php

namespace Excellence\Geoip\Model;

/**
 * Geoip Model
 *
 * @method \Excellence\Geoip\Model\Resource\Page _getResource()
 * @method \Excellence\Geoip\Model\Resource\Page getResource()
 */
class Geoip extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Excellence\Geoip\Model\ResourceModel\Geoip');
    }

}
