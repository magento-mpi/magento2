<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleNew;
use Magento\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;

/**
 * Class AssertBannerNotInCatalogRule
 * Assert that deleted banner is absent on catalog rule creation page and can't be found by name
 */
class AssertBannerNotInCatalogRule extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that deleted banner is absent on catalog rule creation page and can't be found by name
     *
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleNew $ruleNew
     * @param BannerInjectable $banner
     * @return void
     */
    public function processAssert(
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleNew $ruleNew,
        BannerInjectable $banner
    ) {
        $catalogRuleIndex->open();
        $catalogRuleIndex->getGridPageActions()->addNew();
        $ruleNew->getEditForm()->openTab('related_banners');
        $filter = ['banner_name' => $banner->getName()];
        \PHPUnit_Framework_Assert::assertFalse(
            $ruleNew->getEditForm()->getTabElement('related_banners')->getBannersGrid()->isRowVisible($filter),
            'Banner is present in Catalog Price Rule grid.'
        );
    }

    /**
     * Banner is absent in Catalog Price Rule grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner is absent in Catalog Price Rule grid.';
    }
}
