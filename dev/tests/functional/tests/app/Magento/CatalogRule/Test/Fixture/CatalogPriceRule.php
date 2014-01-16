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

namespace Magento\CatalogRule\Test\Fixture;

use Magento\CatalogRule\Test\Repository\CatalogPriceRule as Repository;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Class CatalogPriceRule
 *
 * @package Magento\CatalogRule\Test\Fixture
 */
class CatalogPriceRule extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogRuleCatalogPriceRule($this->_dataConfig, $this->_data);

        //Default data set
        $this->switchData(Repository::CATALOG_PRICE_RULE);
    }

    /**
     * Get the rule name value
     */
    public function getRuleName()
    {
        return $this->getData('fields/rule_name/value');
    }

    /**
     * Get the discount amount value
     */
    public function getDiscountAmount()
    {
        return $this->getData('fields/rule_discount_amount/value');
    }
}

