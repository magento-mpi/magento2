<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sitemap\Test\Page\Adminhtml\AdminSitemapIndex;

/**
 * Class AssertSitemapSuccessSaveMessage
 *
 * @package Magento\Sitemap\Test\Constraint
 */
class AssertSitemapSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The sitemap has been saved.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after sitemap save
     *
     * @param AdminSitemapIndex $sitemapPage
     * @return void
     */
    public function processAssert(AdminSitemapIndex $sitemapPage)
    {
        $actualMessage = $sitemapPage->getSitemapSaveMessage()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
