<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Test\Fixture;

use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

/**
 * Class Method
 * Shipping methods
 *
 */
class Method extends DataFixture
{
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoShippingMethod($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('flat_rate');
    }
}
