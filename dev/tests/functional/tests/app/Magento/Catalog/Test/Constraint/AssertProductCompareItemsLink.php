<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Class AssertProductCompareItemsLink
 */
class AssertProductCompareItemsLink extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that link "Compare Products %count_of_compared_products% item"(is count >1 ""Compare Products
     * %count_of_compared_products% items") is presented at the top of the page
     * (near "welcom msg", My Account, Register ...). Link contains correct count of products that added
     * to compare. Link is lead to Compare Product Page.
     *
     * @param array $products
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(array $products, CmsIndex $cmsIndex)
    {
        $linkQtyTextFixture = count($products);
        $linkQtyTextPage = $cmsIndex->getLinksBlock()->getQtyCompareProducts();

        \PHPUnit_Framework_Assert::assertEquals(
            $linkQtyTextFixture,
            $linkQtyTextPage,
            'That link "Compare Products ' . $linkQtyTextFixture . ' item" not correct.'
        );

        $linkQtyHrefFixture = '/catalog/product_compare/';
        $linkQtyHrefPage = $cmsIndex->getLinksBlock()->getLinkUrl('Compare Products');
        $isValidLink = strpos($linkQtyHrefPage, $linkQtyHrefFixture);

        \PHPUnit_Framework_Assert::assertTrue(
            is_numeric($isValidLink),
            'That link isn\'t lead to Compare Product Page.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'That link is correct.';
    }
}
