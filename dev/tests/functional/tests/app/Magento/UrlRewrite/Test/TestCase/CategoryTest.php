<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\TestCase;

use Mtf\Factory\Factory;
use Magento\UrlRewrite\Test\Fixture\UrlRewriteCategory;
use Mtf\TestCase\Injectable;

/**
 * Class UrlrewriteTest
 * Category URL rewrite creation test
 */
class CategoryTest extends Injectable
{
    public function __inject()
    {
        //
    }

    /**
     * Adding permanent redirect for category
     *
     * @ZephyrId MAGETWO-12407
     * @param UrlRewriteCategory $urlRewriteCategory
     */
    public function test(\Magento\UrlRewrite\Test\Fixture\UrlRewriteCategory $urlRewriteCategory)
    {
        $urlRewriteCategory->switchData('category_with_permanent_redirect');

        //Pages & Blocks
        $urlRewriteIndexPage = Factory::getPageFactory()->getAdminUrlrewriteIndex();
        $pageActionsBlock = $urlRewriteIndexPage->getPageActionsBlock();
        $urlRewriteEditPage = Factory::getPageFactory()->getAdminUrlrewriteEdit();
        $categoryTreeBlock = $urlRewriteEditPage->getTreeBlock();
        $urlRewriteInfoForm = $urlRewriteEditPage->getFormBlock();

        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $urlRewriteIndexPage->open();
        $pageActionsBlock->addNew();
        $categoryTreeBlock->selectCategory($urlRewriteCategory->getCategoryName());
        $urlRewriteInfoForm->fill($urlRewriteCategory);
        $urlRewriteEditPage->getPageMainActions()->save();
        $this->assertContains(
            'The URL Rewrite has been saved.',
            $urlRewriteIndexPage->getMessagesBlock()->getSuccessMessages()
        );

        $this->assertUrlRedirect(
            $_ENV['app_frontend_url'] . $urlRewriteCategory->getRewrittenRequestPath(),
            $_ENV['app_frontend_url'] . $urlRewriteCategory->getOriginalRequestPath()
        );
    }

    /**
     * Assert that request URL redirects to target URL
     *
     *
     * @param string $requestUrl
     * @param string $targetUrl
     * @param string $message
     */
    protected function assertUrlRedirect($requestUrl, $targetUrl, $message = '')
    {
        $browser = Factory::getClientBrowser();
        $browser->open($requestUrl);
        $this->assertStringStartsWith($targetUrl, $browser->getUrl(), $message);
    }
}
