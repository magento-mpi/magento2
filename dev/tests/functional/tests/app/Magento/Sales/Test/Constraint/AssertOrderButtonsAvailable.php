<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\SalesOrder;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderButtonsAvailable
 * Assert  that specified in data set buttons exist on order page in backend
 */
class AssertOrderButtonsAvailable extends AbstractConstraint
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
     * @param SalesOrder $salesOrder
     * @param string $orderButtonsAvailable
     * @return void
     */
    public function processAssert(SalesOrder $salesOrder, $orderButtonsAvailable)
    {
        $buttons = explode(',', $orderButtonsAvailable);
        $absentButtons = [];
        $actionsBlock = $salesOrder->getOrderActionsBlock();

        foreach ($buttons as $button) {
            $button = trim($button);
            if (!$actionsBlock->isActionButtonVisible($button)) {
                $absentButtons[] = $button;
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty(
            $absentButtons,
            "Next buttons was not found on page: \n" . implode(";\n", $absentButtons)
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
