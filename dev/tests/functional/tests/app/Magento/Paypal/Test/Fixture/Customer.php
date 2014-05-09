<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Test\Fixture;

use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

/**
 * Class Customer
 * Paypal buyer account
 *
 */
class Customer extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoPaypalCustomer($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('customer_US');
    }
}
