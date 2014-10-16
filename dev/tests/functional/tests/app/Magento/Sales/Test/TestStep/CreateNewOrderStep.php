<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class CreateNewOrderStep
 * Create new order
 */
class CreateNewOrderStep implements TestStepInterface
{
    /**
     * Sales order index page
     *
     * @var OrderIndex
     */
    protected $orderIndex;

    /**
     * @constructor
     * @param OrderIndex $orderIndex
     */
    public function __construct(OrderIndex $orderIndex)
    {
        $this->orderIndex = $orderIndex;
    }

    /**
     * Create new order
     *
     * @return void
     */
    public function run()
    {
        $this->orderIndex->getGridPageActions()->addNew();
    }
}
