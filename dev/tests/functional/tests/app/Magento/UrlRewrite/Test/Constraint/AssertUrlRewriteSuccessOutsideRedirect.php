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

/**
 * Class AssertUrlRewriteSuccessOutsideRedirect
 * Assert that outside redirect was success
 */
class AssertUrlRewriteSuccessOutsideRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that outside redirect was success
     *
     * @param UrlRewrite $urlRewrite
     * @param Browser $browser
     * @param UrlRewrite $initialRewrite
     * @return void
     */
    public function processAssert(UrlRewrite $urlRewrite, Browser $browser, UrlRewrite $initialRewrite = null)
    {
        $data = $initialRewrite != null
            ? array_merge($initialRewrite->getData(), $urlRewrite->getData())
            : $urlRewrite->getData();

        $url = $data['target_path'];
        $browser->open($_ENV['app_frontend_url'] . $data['request_path']);
        $browserUrl = $browser->getUrl();

        \PHPUnit_Framework_Assert::assertEquals(
            $browserUrl,
            $url,
            'URL rewrite redirect false.'
            . "\nExpected: " . $url
            . "\nActual: " . $browserUrl
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom outside URL rewrite redirect was success.';
    }
}
