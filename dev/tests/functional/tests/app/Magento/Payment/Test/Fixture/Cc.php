<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Payment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class Cc
 * Credit cards for checkout
 *
 */
class Cc extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoPaymentCc($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('visa_default');
    }

    /**
     * Get Credit Card validation password for 3D Secure
     *
     * @return string
     */
    public function getValidationPassword()
    {
        return $this->getData('validation/password/value');
    }
}
