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
 * Class AssertCatalogPriceRuleIsNotPresentedInGrid
 */
class AssertCatalogPriceRuleIsNotPresentedInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Catalog Price Rule is not presented in grid and cannot be found using ID, Rule name
     *
     * @param CatalogRule $catalogPriceRuleOriginal
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @return void
     */
    public function processAssert(
        CatalogRule $catalogPriceRuleOriginal,
        CatalogRuleIndex $pageCatalogRuleIndex
    ) {
        $filter = [
            'rule_id' => $catalogPriceRuleOriginal->getId(),
            'name' => $catalogPriceRuleOriginal->getName(),
        ];
        $pageCatalogRuleIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $pageCatalogRuleIndex->getCatalogRuleGrid()->isRowVisible($filter),
            'Catalog Price Rule \'' . $filter['rule_id'] . '\', '
            . 'with name \'' . $filter['name'] . '\', '
            . 'is present in Catalog Price Rule grid.'
        );
    }

    /**
     * Success text that Catalog Price Rule is NOT present in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Price Rule is NOT present in Catalog Rule grid.';
    }
}
