<?php

namespace Excellence\Geoip\Model\Adminhtml\Config\Source;
 
class RedirectRule implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            '0' => __('All URLs')
            ,'1' => __('Only Home Page')
            ,'2' => __('Specified URLs')
            ,'3' => __('Except Specified URLs')
            ];
    }
}
