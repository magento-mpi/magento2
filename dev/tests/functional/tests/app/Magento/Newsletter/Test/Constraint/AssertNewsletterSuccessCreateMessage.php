<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Constraint;

use Magento\Newsletter\Test\Page\Adminhtml\NewsletterTemplateIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertNewsletterSuccessCreateMessage
 *
 * @package Magento\Newsletter\Test\Constraint
 */
class AssertNewsletterSuccessCreateMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The newsletter template has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     *  Assert that success message is displayed after newsletter template save
     *
     * @param NewsletterTemplateIndex $newsletterTemplateIndex
     */
    public function processAssert(NewsletterTemplateIndex $newsletterTemplateIndex)
    {
        $actualMessage = $newsletterTemplateIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Success assert of created newsletter template success message
     *
     * @return string
     */
    public function toString()
    {
        return 'Newsletter success save message is present.';
    }
}
