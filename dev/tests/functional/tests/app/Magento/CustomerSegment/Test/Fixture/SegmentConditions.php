<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class SegmentCondition
 *
 */
class SegmentConditions extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerSegmentSegmentConditions($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('retailer_condition');
    }

    /**
     * Get condition type
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->getData('fields/conditions__1__new_child/value');
    }

    /**
     * Get condition value
     *
     * @return string
     */
    public function getConditionValue()
    {
        return $this->getData('fields/conditions__1--1__value/value');
    }
}
