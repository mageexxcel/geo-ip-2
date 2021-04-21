<?php
namespace Excellence\Geoip\Model\ResourceModel;
class IpLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('excellence_geoip_iplog','excellence_geoip_iplog_id');
    }
}
