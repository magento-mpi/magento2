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
     * @param CatalogRule $catalogPriceRuleOriginal
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogRuleIndex $pageCatalogRuleIndex,
        CatalogRule $catalogPriceRuleOriginal = null
    ) {
        $data = $catalogPriceRule->getData();
        if ($catalogPriceRuleOriginal !== null) {
            $data['rule_id'] = (!isset($data['rule_id'])) ? $catalogPriceRuleOriginal->getId() : $data['rule_id'];
            $data['name'] = (!isset($data['name'])) ? $catalogPriceRuleOriginal->getName() : $data['name'];
            $data['is_active'] = (!isset($data['is_active'])) ?
                $catalogPriceRuleOriginal->getIsActive() : $data['is_active'];
            $filter = [
                'rule_id' => $data['rule_id'],
                'name' => $data['name'],
                'is_active' => $data['is_active'],
            ];
        } else {
            $filter = [
                'name' => $data['name'],
                'is_active' => $data['is_active'],
            ];
        }
        //add rule_website to filter if there is one
        if ($catalogPriceRule->getWebsiteIds() != null) {
            $rule_website = $catalogPriceRule->getWebsiteIds();
            $rule_website = reset($rule_website);
            $filter['rule_website'] = $rule_website;
        }
        //add from_date & to_date to filter if there are ones
        if (isset($data['from_date']) && isset($data['to_date'])) {
            $dateArray['from_date'] = date("M j, Y", strtotime($catalogPriceRule->getFromDate()));
            $dateArray['to_date'] = date("M j, Y", strtotime($catalogPriceRule->getToDate()));
            $filter = array_merge($filter, $dateArray);
        }

        $pageCatalogRuleIndex->open();
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCatalogRuleIndex->getCatalogRuleGrid()->isRowVisible($filter),
            'Catalog Price Rule with following data: \'' . $errorMessage . '\' '
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
