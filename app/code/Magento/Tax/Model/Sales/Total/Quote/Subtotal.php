<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Calculate items and address amounts including/excluding tax
 */
namespace Magento\Tax\Model\Sales\Total\Quote;

use Magento\Sales\Model\Quote\Address;
use Magento\Sales\Model\Quote\Item\AbstractItem;
use Magento\Tax\Model\Calculation;

class Subtotal extends Tax
{
    /**
     * {@inheritdoc}
     */
    protected function includeShipping()
    {
        return false;
    }


    /**
     * Override the behavior in Tax collector to return empty array
     *
     * @param Address $address
     * @return array
     */
    public function fetch(Address $address)
    {
        return [];
    }
}
