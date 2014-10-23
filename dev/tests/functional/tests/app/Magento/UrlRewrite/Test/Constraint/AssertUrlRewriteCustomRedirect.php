<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;

/**
 * Class AssertUrlRewriteCustomRedirect
 * Assert check URL rewrite custom redirect
 */
class AssertUrlRewriteCustomRedirect extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert check URL rewrite custom redirect
     *
     * @param UrlRewrite $urlRewrite
     * @param Browser $browser
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(UrlRewrite $urlRewrite, Browser $browser, CmsIndex $cmsIndex)
    {
        $browser->open($_ENV['app_frontend_url'] . $urlRewrite->getRequestPath());
        $entity = $urlRewrite->getDataFieldConfig('target_path')['source']->getEntity();
        $title = $entity->hasData('name') ? $entity->getName() : $entity->getTitle();
        $pageTitle = $cmsIndex->getTitleBlock()->getTitle();
        \PHPUnit_Framework_Assert::assertEquals(
            $pageTitle,
            $title,
            'URL rewrite product redirect false.'
            . "\nExpected: " . $title
            . "\nActual: " . $pageTitle
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom URL rewrite redirect was success.';
    }
}
