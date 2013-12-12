<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Fixture;

use Mtf\Fixture\DataFixture;
use Mtf\Factory\Factory;

class DeleteSalesRule extends DataFixture
{
    private $salesRuleId;

    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->salesRuleId = -1;
    }

    public function getSalesRuleId()
    {
        return $this->salesRuleId;
    }

    public function setSalesRuleId($srid)
    {
        $this->salesRuleId = $srid;
    }

    public function persist()
    {
        Factory::getApp()->magentoSalesRuleDeleteSalesRule($this);
    }
}
