<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Fields:
 * - carrier: ups
 * - carrierTitle: United Parcel Service
 * - method: 2day
 * - methodTitle: UPS 2nd Day Priority
 * - price: $9.40 (cost+handling)
 * - cost: $8.00
 */
namespace Magento\Shipping\Model\Rate\Result;

class Method extends \Magento\Shipping\Model\Rate\Result\AbstractResult
{
    /**
     * Round shipping carrier's method price
     *
     * @param string|float|int $price
     * @return \Magento\Shipping\Model\Rate\Result\Method
     */
    public function setPrice($price)
    {
        $this->setData('price', \Mage::app()->getStore()->roundPrice($price));
        return $this;
    }
}
