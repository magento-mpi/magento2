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

namespace Magento\CustomerSegment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class SegmentCondition
 *
 * @package Magento\CustomerSegment\Test\Fixture
 */
class SegmentCondition extends DataFixture {
    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerSegmentConditions($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('retailer_condition');
    }
}