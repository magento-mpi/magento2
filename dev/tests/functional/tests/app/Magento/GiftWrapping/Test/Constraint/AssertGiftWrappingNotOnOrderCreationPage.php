<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class AssertGiftWrappingNotOnOrderCreationPage
 * Assert that deleted Gift Wrapping can not be found on order creation page in backend
 */
class AssertGiftWrappingNotOnOrderCreationPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted Gift Wrapping can not be found on order creation page in backend
     *
     * @param OrderIndex $orderIndex
     * @param OrderCreateIndex $orderCreateIndex
     * @param GiftWrapping $giftWrapping
     * @return void
     */
    public function processAssert(
        OrderIndex $orderIndex,
        OrderCreateIndex $orderCreateIndex,
        GiftWrapping $giftWrapping
    ) {
        $orderIndex->open();
        $orderIndex->getGridPageActions()->addNew();
        $orderCreateIndex->getCustomerBlock()->selectCustomer(null);
        \PHPUnit_Framework_Assert::assertFalse(
            $orderCreateIndex->getGiftOptionsBlock()->isGiftWrappingAvailable($giftWrapping->getDesign()),
            'Gift Wrapping \'' . $giftWrapping->getDesign() . '\' is present on order creation page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Wrapping can not be found on order creation page in backend.';
    }
}
