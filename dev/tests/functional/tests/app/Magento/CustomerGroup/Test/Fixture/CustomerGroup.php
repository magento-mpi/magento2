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
namespace Magento\CustomerGroup\Test\Fixture;

use Magento\CustomerGroup\Test\Repository\CustomerGroup as Repository;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class CustomerSegment
 *
 * @package Magento\CustomerGroup\Test\Fixture
 */
class CustomerGroup extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCustomerGroupCustomerGroup($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData(Repository::CUSTOMER_GROUP_CURL);
    }

    /**
     * Create customer group in the backend
     */
    public function persist()
    {
        Factory::getApp()->magentoCustomerGroupCreateCustomerGroup($this);
    }
}
