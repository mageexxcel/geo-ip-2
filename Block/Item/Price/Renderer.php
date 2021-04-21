<?php
/**
 * Copyright © 2017 xMagestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Excellence\Geoip\Block\Item\Price;

class Renderer extends \Magento\Weee\Block\Item\Price\Renderer
{
	/**
     * Get display price for row total excluding tax. The Weee amount will be added to row total
     * depending on Weee display setting
     *
     * @return float
     */
    public function getRowDisplayPriceExclTax()
    {
        $rowTotalExclTax = $this->getItemDisplayPriceExclTax();

        if (!$this->weeeHelper->isEnabled($this->getStoreId())) {
            return $rowTotalExclTax;
        }

        if ($this->getIncludeWeeeFlag()) {
            return $rowTotalExclTax + $this->weeeHelper->getWeeeTaxAppliedRowAmount($this->getItem());
        }

        return $rowTotalExclTax;
    }
}