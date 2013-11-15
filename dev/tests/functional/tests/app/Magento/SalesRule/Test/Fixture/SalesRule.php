<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Fixture;

use Magento\SalesRule\Test\Repository\SalesRule as Repository;
use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

class SalesRule extends DataFixture
{

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoSalesRuleSalesRule($this->_dataConfig, $this->_data);
        $this->switchData(Repository::SIMPLE);
    }

    /**
     * Return the name of the sales rule represented by this fixture
     *
     * @return string
     */
    public function getSalesRuleName()
    {
        return $this->getData('fields/name/value');
    }
}
