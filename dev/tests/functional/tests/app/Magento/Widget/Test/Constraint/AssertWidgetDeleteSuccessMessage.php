<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;

/**
 * Class AssertWidgetDeleteSuccessMessage
 * Check that message presents "The widget instance has been deleted."
 */
class AssertWidgetDeleteSuccessMessage extends AbstractConstraint
{
    const DELETE_MESSAGE = 'The widget instance has been deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert message presents "The widget instance has been deleted."
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(WidgetInstanceIndex $widgetInstanceIndex, AdminCache $adminCache)
    {
        $actualMessage = $widgetInstanceIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );

        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->assertSuccessMessage();
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
