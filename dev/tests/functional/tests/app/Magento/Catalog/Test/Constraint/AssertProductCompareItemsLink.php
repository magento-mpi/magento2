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
        $productQty = count($products);
        $qtyOnPage = $cmsIndex->getLinksBlock()->getQtyInCompareList();

        \PHPUnit_Framework_Assert::assertEquals(
            $productQty,
            $qtyOnPage,
            'Actually you verify product qty in the compare product list, not a whole link.'
        );

        $compareProductUrl = '/catalog/product_compare/';
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($cmsIndex->getLinksBlock()->getLinkUrl('Compare Products'), $compareProductUrl) !== false,
            'Compare product link isn\'t lead to Compare Product Page.'
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
