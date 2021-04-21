<?php
namespace Excellence\Geoip\Model\ResourceModel\IpLog;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Excellence\Geoip\Model\IpLog','Excellence\Geoip\Model\ResourceModel\IpLog');
    }
}
