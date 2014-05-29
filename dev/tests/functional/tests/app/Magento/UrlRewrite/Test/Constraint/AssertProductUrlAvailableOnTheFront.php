<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductUrlAvailableOnTheFront
 * Assert that product available by new URL on the front
 */
class AssertProductUrlAvailableOnTheFront extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Check that product available by new URL on the front
     *
     * @param UrlRewrite $urlRewrite
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        UrlRewrite $urlRewrite,
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $urlRewrite->getRequestPath());
        \PHPUnit_Framework_Assert::assertEquals(
            $catalogProductView->getTitleBlock()->getTitle(),
            $product->getName(),
            'URL rewrite product redirect false.'
            . "\nExpected: " . $product->getName()
            . "\nActual: " . $catalogProductView->getTitleBlock()->getTitle()
        );
    }

    /**
     * Product available by new URL on the front
     *
     * @return string
     */
    public function toString()
    {
        return 'Product available by new URL on the front.';
    }
}
