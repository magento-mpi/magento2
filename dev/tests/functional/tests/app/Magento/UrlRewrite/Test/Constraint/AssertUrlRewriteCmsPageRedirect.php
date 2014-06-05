<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;
use Magento\Cms\Test\Fixture\CmsPage;

/**
 * Class AssertUrlRewriteCmsPageRedirect
 * Assert that created CMS Page URL Redirect lead to appropriate page in frontend
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
     * URL for CMS Page
     *
     * @var string
     */
    protected $url = 'cms/page/view/page_id/';

    /**
     * Assert that created CMS Page URL Redirect lead to appropriate page in frontend
     *
     * @param UrlRewrite $urlRewrite
     * @param CmsPage $cmsPage
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        UrlRewrite $urlRewrite,
        CmsPage $cmsPage,
        Browser $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $urlRewrite->getRequestPath());
        $url = $urlRewrite->getOptions() == 'No'
            ? $urlRewrite->getRequestPath()
            : $this->url . $cmsPage->getPageId();

        \PHPUnit_Framework_Assert::assertEquals(
            $browser->getUrl(),
            $_ENV['app_frontend_url'] . $url,
            'URL rewrite CMS Page redirect false.'
            . "\nExpected: " . $_ENV['app_frontend_url'] . $url
            . "\nActual: " . $browser->getUrl()
        );
    }

    /**
     * URL Redirect lead to appropriate page in frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'URL Redirect lead to appropriate page in frontend.';
    }
}
