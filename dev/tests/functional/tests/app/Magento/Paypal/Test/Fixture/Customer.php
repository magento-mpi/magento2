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

namespace Magento\Paypal\Test\Fixture;

use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

/**
 * Class Customer
 * Paypal buyer account
 *
 * @package Magento\Paypal\Test\Fixture
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
