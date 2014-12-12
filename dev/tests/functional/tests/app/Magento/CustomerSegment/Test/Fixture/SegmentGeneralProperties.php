<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class CustomerSegment
 *
 */
class SegmentGeneralProperties extends DataFixture
{
    /**
     * Get segment name
     *
     * @return string
     */
    public function getSegmentName()
    {
        return $this->getData('fields/name/value');
    }

    /**
     * Get segment description
     *
     * @return string
     */
    public function getSegmentDescription()
    {
        return $this->getData('fields/description/value');
    }

    /**
     * Get segment website ids
     *
     * @return string
     */
    public function getSegmentWebsiteIds()
    {
        return $this->getData('fields/website_ids/value');
    }

    /**
     * Get segment is active
     *
     * @return string
     */
    public function getSegmentIsActive()
    {
        return $this->getData('fields/is_active/value');
    }

    /**
     * Get segment is apply to
     *
     * @return string
     */
    public function getSegmentApplyTo()
    {
        return $this->getData('fields/apply_to/value');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerSegmentSegmentGeneralProperties($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData('all_retail_customers');
    }
}
