<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Class AssertWidgetSuccessDeleteMessage
 * Check that Widget success delete message presents
 */
class AssertWidgetSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Message displayed after delete widget
     */
    const DELETE_MESSAGE = 'The widget instance has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Widget success delete message is present
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @return void
     */
    public function processAssert(WidgetInstanceIndex $widgetInstanceIndex)
    {
        $actualMessage = $widgetInstanceIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $actualMessage,
            'Wrong widget success delete message is displayed.'
        );
    }

    /**
     * Text of Delete Widget Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Widget success delete message is present.';
    }
}
