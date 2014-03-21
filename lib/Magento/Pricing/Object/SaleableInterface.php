<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Object;

interface SaleableInterface
{
    /**
     * @return \Magento\Pricing\PriceInfoInterface
     */
    public function getPriceInfo();

    /**
     * @return string
     */
    public function getTypeId();
}
