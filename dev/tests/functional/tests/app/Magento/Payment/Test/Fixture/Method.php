<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Payment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Method
 * Shipping methods
 *
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
     * Get payment action
     *
     * @return null|string
     */
    public function getPaymentAction()
    {
        return $this->getData('fields/payment_action');
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
