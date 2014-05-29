<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Test Creation for Update Product URL Rewrites Entity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create custom store view
 * 2. Create simple product
 * 3. Create product UrlRewrite
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Marketing->Url Redirects
 * 3. Search and open created Url Redirect
 * 4. Fill data according to dataset
 * 5. Perform all assertions
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-24819
 */
class UpdateProductURLRewritesEntityTest extends Injectable
{
    /**
     * Url rewrite index page
     *
     * @var UrlrewriteIndex
     */
    protected $urlRewriteIndex;

    /**
     * Url rewrite edit page
     *
     * @var UrlrewriteEdit
     */
    protected $urlRewriteEdit;

    /**
     * Prepare dataSets and pages
     *
     * @param FixtureInterface $product
     * @param FixtureFactory $fixtureFactory
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @return array
     */
    public function __inject(
        FixtureInterface $product,
        FixtureFactory $fixtureFactory,
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;

        $product->persist();
        /** @var UrlRewrite $productRedirect */
        $productRedirect = $fixtureFactory->createByCode(
            'urlRewrite',
            [
                'dataSet' => 'default',
                'data' => ['rewrite_path' => 'product/' . $product->getId()]
            ]
        );
        $productRedirect->persist();

        return ['productRedirect' => $productRedirect, 'product' => $product];
    }

    /**
     * Update product URL rewrites
     *
     * @param UrlRewrite $urlRewrite
     * @param UrlRewrite $productRedirect
     * @return void
     */
    public function testUpdateProductURLRewrites(UrlRewrite $urlRewrite, UrlRewrite $productRedirect)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $productRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}
