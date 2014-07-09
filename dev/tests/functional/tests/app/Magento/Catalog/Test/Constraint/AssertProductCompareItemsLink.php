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
     * Assert that link "Compare Products..." on top menu of page
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

        \PHPUnit_Framework_Assert::assertTrue(
            strpos($linkQtyHrefPage, $linkQtyHrefFixture) !== false,
            'That link isn\'t lead to Compare Product Page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'That link is correct.';
    }
}
