<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Customer in Backend
 *
 * @package Magento\Customer\Test\Fixture
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
