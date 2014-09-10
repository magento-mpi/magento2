<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertNoButtons
 * Assert that buttons from dataSet are not present on page
 */
class AssertNoButtons extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that buttons from dataSet are not present on page
     *
     * @param OrderIndex $orderIndex
     * @param OrderView $orderView
     * @param OrderInjectable $order
     * @param string $buttons
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex, OrderView $orderView, OrderInjectable $order, $buttons)
    {
        $orderIndex->open();
        $orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $order->getId()]);
        $buttons = explode(',', $buttons);
        $matches = [];
        foreach ($buttons as $button) {
            if ($orderView->getPageActions()->isActionButtonVisible(trim($button))) {
                $matches[] = $button;
            }
        }
        \PHPUnit_Framework_Assert::assertEmpty(
            $matches,
            'Buttons are present on order page.'
            . "\nLog:\n" . implode(";\n", $matches)
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Buttons from dataSet are not present on order page.';
    }
}
