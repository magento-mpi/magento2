<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Class CreateOrderFromCustomerAccountStep
 * Create order from customer
 */
class CreateOrderFromCustomerAccountStep implements TestStepInterface
{
    /**
     * Customer edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * @constructor
     * @param CustomerIndexEdit $customerIndexEdit
     */
    public function __construct(CustomerIndexEdit $customerIndexEdit)
    {
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Create new order from customer step
     *
     * @return void
     */
    public function run()
    {
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
    }
}
