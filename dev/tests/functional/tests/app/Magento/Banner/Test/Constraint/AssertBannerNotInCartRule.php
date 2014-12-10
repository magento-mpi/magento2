<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerNotInCartRule
 * Assert that deleted banner is absent on shopping cart rule creation page and can't be found by name
 */
class AssertBannerNotInCartRule extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted banner is absent on shopping cart rule creation page and can't be found by name
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteNew $quoteNew
     * @param BannerInjectable $banner
     * @return void
     */
    public function processAssert(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteNew $quoteNew,
        BannerInjectable $banner
    ) {
        $promoQuoteIndex->open();
        $promoQuoteIndex->getGridPageActions()->addNew();
        $form = $quoteNew->getSalesRuleForm();
        $form->openTab('related_banners');
        $filter = ['banner_name' => $banner->getName()];
        \PHPUnit_Framework_Assert::assertFalse(
            $form->getTabElement('related_banners')->getBannersGrid()->isRowVisible($filter),
            'Banner is present in Cart Price Rule grid.'
        );
    }

    /**
     * Banner is absent in Cart Price Rule grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner is absent in Cart Price Rule grid.';
    }
}
