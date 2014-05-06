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
 * Class AssertCatalogPriceRuleInGrid
 *
 * @package Magento\CatalogRule\Test\Constraint
 */
class AssertCatalogPriceRuleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that data in grid on Catalog Price Rules page according to fixture
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogRuleIndex $pageCatalogRuleIndex
     * @return void
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogRuleIndex $pageCatalogRuleIndex
    ) {
        $rule_website = $catalogPriceRule->getWebsiteIds();
        $rule_website = reset($rule_website);
        $filter = [
            'name' => $catalogPriceRule->getName(),
            'is_active' => $catalogPriceRule->getIsActive(),
            'rule_website' => $rule_website,
        ];

        $pageCatalogRuleIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCatalogRuleIndex->getCatalogRuleGrid()->isRowVisible($filter),
            'Catalog Price Rule \'' . $filter['name'] . '\', '
            . 'with status \'' . $filter['is_active'] . '\', '
            . 'website \''. $rule_website . '\' '
            . 'is absent in Catalog Price Rule grid.'
        );
    }

    /**
     * Success text that Catalog Price Rule exists in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Catalog Price Rule is present in Catalog Rule grid.';
    }
}
