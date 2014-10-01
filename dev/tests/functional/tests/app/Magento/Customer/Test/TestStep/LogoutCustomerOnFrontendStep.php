<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Customer\Test\Page\CustomerAccountLogout;

/**
 * Class LogoutCustomerOnFrontendStep
 * Logout customer on frontend
 */
class LogoutCustomerOnFrontendStep implements TestStepInterface
{
    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * @constructor
     * @param CustomerAccountLogout $customerAccountLogout
     */
    public function __construct(
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Logout customer
     *
     * @return void
     */
    public function run()
    {
        $this->customerAccountLogout->open();
    }
}
