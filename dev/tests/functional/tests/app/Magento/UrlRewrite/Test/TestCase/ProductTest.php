<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\UrlRewrite\Test\Fixture\UrlRewriteProduct;

/**
 * Class UrlRewriteTest
 * Product URL rewrite creation test
 */
class ProductTest extends Functional
{
    /**
     * Adding temporary redirect for product
     *
     * @return void
     * @ZephyrId MAGETWO-12409
     */
    public function testUrlRewriteCreation()
    {
        /** @var UrlRewriteProduct $urlRewriteProduct */
        $urlRewriteProduct = Factory::getFixtureFactory()->getMagentoUrlRewriteUrlRewriteProduct();
        $urlRewriteProduct->switchData('product_with_temporary_redirect');

        //Pages & Blocks
        $urlRewriteGridPage = Factory::getPageFactory()->getAdminUrlRewriteIndex();
        $pageActionsBlock = $urlRewriteGridPage->getPageActionsBlock();
        $urlRewriteEditPage = Factory::getPageFactory()->getAdminUrlRewriteEdit();
        $categoryTreeBlock = $urlRewriteEditPage->getTreeBlock();
        $productGridBlock = $urlRewriteEditPage->getProductGridBlock();
        $typeSelectorBlock = $urlRewriteEditPage->getUrlRewriteTypeSelectorBlock();
        $urlRewriteInfoForm = $urlRewriteEditPage->getFormBlock();

        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $urlRewriteGridPage->open();
        $pageActionsBlock->addNew();
        $typeSelectorBlock->selectType($urlRewriteProduct->getUrlRewriteType());
        $productGridBlock->searchAndSelect(array('sku' => $urlRewriteProduct->getProductSku()));
        $categoryTreeBlock->selectCategory($urlRewriteProduct->getCategoryName());
        $urlRewriteInfoForm->fill($urlRewriteProduct);
        $urlRewriteEditPage->getPageMainActions()->save();
        $this->assertContains(
            'The URL Rewrite has been saved.',
            $urlRewriteGridPage->getMessagesBlock()->getSuccessMessages()
        );

        $this->assertUrlRewrite(
            $_ENV['app_frontend_url'] . $urlRewriteProduct->getRewrittenRequestPath(),
            $_ENV['app_frontend_url'] . $urlRewriteProduct->getOriginalRequestPath()
        );
    }

    /**
     * Assert that request URL redirects to target URL
     *
     * @param string $requestUrl
     * @param string $targetUrl
     * @param string $message
     * @return void
     */
    protected function assertUrlRewrite($requestUrl, $targetUrl, $message = '')
    {
        $browser = Factory::getClientBrowser();
        $browser->open($requestUrl);
        $this->assertStringStartsWith($targetUrl, $browser->getUrl(), $message);
    }
}
