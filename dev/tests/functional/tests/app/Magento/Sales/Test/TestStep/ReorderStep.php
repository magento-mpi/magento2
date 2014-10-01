<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderView;

/**
 * Class ReorderStep
 * Click reorder from order on backend
 */
class ReorderStep implements TestStepInterface
{
    /**
     * Order View Page
     *
     * @var OrderView
     */
    protected $orderView;

    /**
     * @construct
     * @param OrderView $orderView
     */
    public function __construct(OrderView $orderView)
    {
        $this->orderView = $orderView;
    }

    /**
     * Click reorder
     *
     * @return void
     */
    public function run()
    {
        $this->orderView->getPageActions()->reorder();
    }
}
