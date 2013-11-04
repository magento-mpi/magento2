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

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Fixture with all necessary data for order creation on backend
 * and existing customer
 *
 * @package Magento\Sales\Test\Fixture
 */
class OrderWithCustomer extends Order
{
    /**
     * {@inheritdoc}
     */
    public function persist()
    {
        parent::persist();
        $this->customer = Factory::getFixtureFactory()->getMagentoCustomerCustomer();
        $this->customer->switchData('backend_customer');
        $this->customer->persist();
    }
}
