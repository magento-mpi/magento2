<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Sales\Total\Quote;

use Magento\Sales\Model\Quote\Address;
use Magento\Tax\Model\Calculation;

class Shipping extends Tax
{
    /**
     * {@inheritdoc}
     */
    protected function includeShipping()
    {
        return true;
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
