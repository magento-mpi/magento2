<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;

/**
 * Class SubmitOrderStep
 * Submit Order
 */
class SubmitOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Sales order view
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param OrderView $orderView
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, OrderView $orderView)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->orderView = $orderView;
    }

    /**
     * Fill Sales Data
     *
     * @return array
     */
    public function run()
    {
        $this->orderCreateIndex->getCreateBlock()->submitOrder();
        $this->orderView->getMessagesBlock()->waitSuccessMessage();
        return['orderId' => trim($this->orderView->getTitleBlock()->getTitle(), '#')];
    }
}
