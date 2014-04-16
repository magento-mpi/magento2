<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Customer\Test\Page\Adminhtml\CustomerGroupIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerGroupNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for CreateCustomerGroupEntity
 *
 * Test Flow:
 * 1.Log in to backend as admin user.
 * 2.Navigate to Stores>Other Settings>Customer Groups.
 * 3.Start to create new Customer Group.
 * 4.Fill in all data according to data set.
 * 5.Click "Save Customer Group" button.
 * 6.Perform all assertions.
 *
 * @group Customer_Groups_(MX)
 * @ZephyrId MTA-42
 */
class CreateCustomerGroupEntityTest extends Injectable
{
    /**
     * Customer group index
     *
     * @var CustomerGroupIndex
     */
    protected $customerGroupIndex;

    /**
     * New customer group
     *
     * @var CustomerGroupIndex
     */
    protected $customerGroupNew;

    /**
     * Customer group grid
     *
     * @var CustomerGroup
     */
    protected $customerGroup;

    /**
     * @param CustomerGroupIndex $customerGroupIndex
     * @param CustomerGroupNew $customerGroupNew
     * @param CustomerGroup $customerGroup
     */
    public function __inject(
        CustomerGroupIndex $customerGroupIndex,
        CustomerGroupNew $customerGroupNew,
        CustomerGroup $customerGroup
    ) {
        $this->customerGroup = $customerGroup;
        $this->customerGroupIndex = $customerGroupIndex;
        $this->customerGroupNew = $customerGroupNew;
    }

    /**
     * Create customer group
     *
     * @param CustomerGroupIndex $customerGroupIndex
     * @param CustomerGroupNew $customerGroupNew
     * @param CustomerGroup $customerGroup
     */
    public function testCreateCustomerGroup(
        CustomerGroupIndex $customerGroupIndex,
        CustomerGroupNew $customerGroupNew,
        CustomerGroup $customerGroup
    ) {
        $customerGroup->persist();

        //Steps
        $customerGroupIndex->open();
        $customerGroupIndex->getGridPageActions()->addNew();
        $customerGroupNew->getPageMainForm()->fill($customerGroup);
        $customerGroupNew->getPageMainActions()->save();
    }
}
