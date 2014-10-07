<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\OrderView;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveOrders;

/**
 * Class AssertArchiveOrderAvailableButtons
 * Assert that specified in data set buttons exist on archived order page in backend
 */
class AssertArchiveOrderAvailableButtons extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that specified in data set buttons exist on order page in backend
     *
     * @param OrderInjectable $order
     * @param ArchiveOrders $archiveOrders
     * @param OrderView $orderView
     * @param string $orderButtonsAvailable
     * @param string $orderStatus
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        ArchiveOrders $archiveOrders,
        OrderView $orderView,
        $orderButtonsAvailable,
        $orderStatus
    ) {
        $filter = [
            'id' => $order->getId(),
            'status' => $orderStatus,
        ];
        $archiveOrders->open();
        $archiveOrders->getSalesOrderGrid()->searchAndOpen($filter);
        $actionsBlock = $orderView->getPageActions();

        $buttons = explode(',', $orderButtonsAvailable);
        $absentButtons = [];

        foreach ($buttons as $button) {
            $button = trim($button);
            if (!$actionsBlock->isActionButtonVisible($button)) {
                $absentButtons[] = $button;
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty(
            $absentButtons,
            "Next buttons were not found on page: \n" . implode(";\n", $absentButtons)
        );
    }

    /**
     * Returns string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return "All buttons are available on order page.";
    }
}
