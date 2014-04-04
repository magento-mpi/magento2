<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Fixture;
use Magento\CatalogRule\Test\Page;

/**
 * Class AssertCatalogRuleInGrid
 *
 * @package Magento\CatalogRule\Test\Constraint
 */
class AssertCatalogRuleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert catalog Rule availability in Catalog Rule Grid
     *
     * @param Fixture\CatalogRule $catalogRule
     * @param Page\CatalogRule $catalogRuleInGrid
     * @return void
     */
    public function processAssert(Fixture\CatalogRule $catalogRule, Page\CatalogRule $catalogRuleInGrid)
    {
        $filter = ['name' => $catalogRule->getName()];
        $catalogRuleInGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogRuleInGrid->getCatalogPriceRuleGridBlock()->isRuleVisible($filter),
            'Product with sku \'' . $catalogRule->getName() . '\' is absent in Products grid.'
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Catalog Rule is present in Catalog Rules grid.';
    }
}
