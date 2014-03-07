<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\TestCase\Urlrewrite;

use Mtf\Factory\Factory,
    Mtf\TestCase\Functional,
    Magento\Backend\Test\Fixture\Urlrewrite\Category;
use Mtf\TestCase\Injectable;

/**
 * Class UrlrewriteTest
 * Category URL rewrite creation test
 *
 * @package Magento\Catalog\Test\TestCase\Category
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
     * @param Category $urlRewriteCategory
     */
    public function test(\Magento\Backend\Test\Fixture\Urlrewrite\Category $urlRewriteCategory)
    {
        $urlRewriteCategory->switchData('category_with_permanent_redirect');

        //Pages & Blocks
        $urlRewriteGridPage = Factory::getPageFactory()->getAdminUrlrewriteIndex();
        $pageActionsBlock = $urlRewriteGridPage->getPageActionsBlock();
        $urlRewriteEditPage = Factory::getPageFactory()->getAdminUrlrewriteEdit();
        $categoryTreeBlock = $urlRewriteEditPage->getCategoryTreeBlock();
        $urlRewriteInfoForm = $urlRewriteEditPage->getUrlRewriteInformationForm();

        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $urlRewriteGridPage->open();
        $pageActionsBlock->addNewUrlRewrite();
        $categoryTreeBlock->selectCategory($urlRewriteCategory->getCategoryName());
        $urlRewriteInfoForm->fill($urlRewriteCategory);
        $urlRewriteInfoForm->save();
        $this->assertContains(
            'The URL Rewrite has been saved.',
            $urlRewriteGridPage->getMessagesBlock()->getSuccessMessages()
        );

        $this->assertUrlRedirect(
            $_ENV['app_frontend_url'] . $urlRewriteCategory->getRewrittenRequestPath(),
            $_ENV['app_frontend_url'] . $urlRewriteCategory->getOriginalRequestPath()
        );
    }

    /**
     * Assert that request URL redirects to target URL
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
