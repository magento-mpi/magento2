<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductCompareBlockOnCmsPage
 */
class AssertProductCompareBlockOnCmsPage extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that Compare Products block is presented on CMS pages.
     * Block contains information about compared products
     *
     * @param array $products
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @return void
     */
    public function processAssert(array $products, CmsIndex $cmsIndex, FixtureFactory $fixtureFactory, Browser $browser)
    {
        $newCmsPage = $fixtureFactory->createByCode('cmsPage', ['dataSet' => '3_column_template']);
        $newCmsPage->persist();
        $browser->open($_ENV['app_frontend_url'] . $newCmsPage->getIdentifier());
        foreach ($products as &$product) {
            $product = $product->getName();
        }
        \PHPUnit_Framework_Assert::assertEquals(
            $products,
            $cmsIndex->getCompareProductsBlock()->getProducts(),
            'Compare product block contains NOT valid information about compared products.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Compare product block contains valid information about compared products.';
    }
}
