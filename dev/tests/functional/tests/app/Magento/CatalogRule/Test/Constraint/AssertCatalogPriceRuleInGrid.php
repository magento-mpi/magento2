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
        //add to filter from_date & to_date if there are ones
        $data = $catalogPriceRule->getData();
        if (isset($data['from_date'])
            && isset($data['to_date'])
        ) {
            $dateArray['from_date'] = date("M j, Y", strtotime($catalogPriceRule->getFromDate()));
            $dateArray['to_date'] = date("M j, Y", strtotime($catalogPriceRule->getToDate()));
            $filter = array_merge($filter, $dateArray);
        }

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
