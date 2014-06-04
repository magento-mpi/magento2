<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteProductUrlRewritesEntity
 *
 * Precondition:
 * 1. Sub category is created.
 * 2. Product is created.
 * 3. Product url rewrites is created.
 *
 * Test Flow:
 * 1. Login to backend.
 * 2. Navigate to MARKETING > URL Redirects.
 * 3. Click Redirect from grid.
 * 4. Click 'Delete' button.
 * 5. Perform asserts.
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId  MAGETWO-23287
 */
class DeleteProductUrlRewriteEntityTest extends Injectable
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
     * @param FixtureFactory $fixtureFactory
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @return array
     */
    public function __inject(
        FixtureFactory $fixtureFactory,
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit
    ) {
        /** @var CatalogProductSimple $product */
        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_without_category']);
        $product->persist();

        /** @var UrlRewrite $productRedirect */
        $productRedirect = $fixtureFactory->createByCode(
            'urlRewrite',
            [
                'dataSet' => 'default',
                'data' => ['id_path' => 'product/' . $product->getId()]
            ]
        );
        $productRedirect->persist();

        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
        return ['productRedirect' => $productRedirect];
    }

    /**
     * Delete product url rewrites entity
     *
     * @param UrlRewrite $productRedirect
     * @return void
     */
    public function testDeleteProductUrlRewrite(UrlRewrite $productRedirect)
    {
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $productRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getPageMainActions()->delete();
    }
}
