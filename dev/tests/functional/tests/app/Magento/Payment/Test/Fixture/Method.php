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

namespace Magento\Payment\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Method
 * Shipping methods
 *
 * @package Magento\Payment\Test\Fixture
 */
class Method extends DataFixture
{
    /**
     * Get payment code
     *
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->getData('fields/payment_code');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoPaymentMethod($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('authorizenet');
    }
}
