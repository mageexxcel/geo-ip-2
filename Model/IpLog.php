<?php
namespace Excellence\Geoip\Model;
class IpLog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'excellence_geoip_iplog';

    protected function _construct()
    {
        $this->_init('Excellence\Geoip\Model\ResourceModel\IpLog');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
