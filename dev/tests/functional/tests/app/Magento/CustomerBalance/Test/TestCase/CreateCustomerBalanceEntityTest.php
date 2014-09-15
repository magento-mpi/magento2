<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\TestCase;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\CustomerBalance\Test\Fixture\CustomerBalance;
use Mtf\TestCase\Injectable;
use \Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Cover creating CustomerBalanceEntity with fucntional tests designed for automation
 *
 * *Precondition:*
 * 1. Default customer is created
 *
 * Test Flow:
 * 1. Login to backend as admin
 * 2. Navigate to CUSTOMERS->All Customers
 * 3. Open customer from preconditions
 * 4. Open "Store Credit" tab
 * 5. Fill form with test data
 * 6. Click "Save Customer" button
 * 7. Preform asserts
 *
 * @group Customers_(MX)
 * @ZephyrId MAGETWO-24387
 */
class CreateCustomerBalanceEntityTest extends Injectable
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Page of all customer grid
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Page of edit customer
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Prepare customer from preconditions
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $this->customer->persist();
        return [
            'customer' => $this->customer,
        ];
    }

    /**
     * Inject customer pages
     *
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function __inject(CustomerIndex $customerIndex, CustomerIndexEdit $customerIndexEdit)
    {
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Create customer balance
     *
     * @param CustomerBalance $customerBalance
     * @return void
     */
    public function test(CustomerBalance $customerBalance)
    {
        $this->customerIndex->open();
        $filter = ['email' => $this->customer->getEmail()];
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen($filter);
        $this->customerIndexEdit->getCustomerBalanceForm()->fill($customerBalance);
        $this->customerIndexEdit->getPageActionsBlock()->save();
    }
}
