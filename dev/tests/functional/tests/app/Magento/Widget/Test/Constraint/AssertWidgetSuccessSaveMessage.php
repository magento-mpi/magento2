<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\AdminCache;
use Magento\Widget\Test\Page\Adminhtml\WidgetInstanceIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertWidgetSuccessSaveMessage
 */
class AssertWidgetSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The widget instance has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after widget saved
     *
     * @param WidgetInstanceIndex $widgetInstanceIndex
     * @param AdminCache $adminCache
     * @return void
     */
    public function processAssert(WidgetInstanceIndex $widgetInstanceIndex, AdminCache $adminCache)
    {
        $actualMessage = $widgetInstanceIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
        );

        // Flush cache
        $adminCache->open();
        $adminCache->getActionsBlock()->flushMagentoCache();
        $adminCache->getMessagesBlock()->waitSuccessMessage();
    }

    /**
     * Text of Created Widget Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Widget success create message is present.';
    }
}
