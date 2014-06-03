<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;

/**
 * Class AssertCatalogRuleInGrid
 *
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
     * @param CatalogRule $catalogRule
     * @param CatalogRuleIndex $catalogRuleGrid
     * @return void
     */
    public function processAssert(CatalogRule $catalogRule, CatalogRuleIndex $catalogRuleGrid)
    {
        $catalogRuleGrid->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogRuleGrid->getCatalogRuleGrid()->isRuleVisible($catalogRule->getName()),
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
