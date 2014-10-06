<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Class AssertBundleProductDetailsInWishlist
 * Assert that the correct option details are displayed on the "View Details" tool tip.
 */
class AssertBundleProductDetailsInWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that the correct option details are displayed on the "View Details" tool tip.
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @param FixtureFactory $fixtureFactory
     * @param MultipleWishlist|null $multipleWishlist [optional]
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product,
        FixtureFactory $fixtureFactory,
        MultipleWishlist $multipleWishlist = null
    ) {
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Wish List');
        if ($multipleWishlist !== null) {
            $wishlistIndex->getManagementBlock()->selectedWishlistByName($multipleWishlist->getName());
        }
        $productBlock = $wishlistIndex->getItemsBlock()->getItemProduct($product);
        $actualOptions = $this->prepareOptions($productBlock->getOptions());
        $cartFixture = $fixtureFactory->createByCode('cart', ['data' => ['items' => ['products' => [$product]]]]);
        $bundleOptions = $cartFixture->getItems()[0]->getData()['options'];
        $expectedOptions = [];
        foreach ($bundleOptions as $option) {
            $expectedOptions[] = $option['value'];
        }

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedOptions,
            $actualOptions,
            "Expected bundle options are not equals to actual"
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Expected bundle options are equal to actual";
    }

    /**
     * Prepare bundle options for comparing
     *
     * @param array $options
     * @return array
     */
    protected function prepareOptions($options)
    {
        foreach ($options as &$option) {
            $chunks = explode(' ', $option);
            $lastChunk = array_pop($chunks);
            $lastChunk = preg_replace("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", '\1', $lastChunk);
            array_push($chunks, $lastChunk);
            $option = implode(' ', $chunks);
        }
        return $options;
    }
}
