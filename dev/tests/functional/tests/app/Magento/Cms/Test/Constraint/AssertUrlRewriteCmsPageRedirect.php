<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Constraint;

use Magento\Cms\Test\Fixture\CmsPage;
use Magento\Cms\Test\Fixture\UrlRewrite;
use Magento\Core\Test\Page\Adminhtml\SystemVariableNew;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUrlRewriteCmsPageRedirect
 * Assert that created CMS Page URL Rewrite lead to appropriate page in frontend
 */
class AssertUrlRewriteCmsPageRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created CMS Page URL Rewrite lead to appropriate page in frontend
     *
     * @param UrlRewrite $urlRewrite
     * @param CmsPage $cmsPage
     * @param SystemVariableNew $systemVariableNew
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        UrlRewrite $urlRewrite,
        CmsPage $cmsPage,
        SystemVariableNew $systemVariableNew,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $urlRewrite->getRequestPath());
        if ($urlRewrite->hasData('store_id')) {
            $store = explode('/', $urlRewrite->getStoreId());
            $systemVariableNew->getFormPageActions()->selectStoreView($store[2]);
        }
        $url = $urlRewrite->getRedirectType() == 'No'
            ? $urlRewrite->getRequestPath()
            : $cmsPage->getTitle();

        \PHPUnit_Framework_Assert::assertEquals(
            $_ENV['app_frontend_url'] . $url,
            $browser->getUrl(),
            'URL rewrite CMS Page redirect false.'
        );
    }

    /**
     * URL Rewrite lead to appropriate page in frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'URL Rewrite lead to appropriate page in frontend.';
    }
}
