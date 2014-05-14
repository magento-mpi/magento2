<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Customer in Backend
 *
 */
class CustomerBackend extends Customer
{
    /**
     * Create customer via backend
     */
    public function persist()
    {
        Factory::getApp()->magentoCustomerCreateCustomerBackend($this);
    }
}
