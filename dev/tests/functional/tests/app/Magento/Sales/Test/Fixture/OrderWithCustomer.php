<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Fixture with all necessary data for order creation on backend
 * and existing customer
 *
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
