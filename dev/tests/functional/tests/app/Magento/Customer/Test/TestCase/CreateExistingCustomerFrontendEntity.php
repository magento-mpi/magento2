<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Test Creation for CreateExistingCustomerFrontendEntity
 *
 * Test Flow:
 * Preconditions:
 *  1.Customer is created
 * Steps:
 * 1. Go to frontend.
 * 2. Click Register link.
 * 3. Fill registry form.
 * 4. Click 'Create account' button.
 * 5. Perform assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MAGETWO-23545
 */
class CreateExistingCustomerFrontendEntity extends Injectable
{
    /**
     * Page CustomerAccountCreate
     *
     * @var CustomerAccountCreate
     */
    protected $customerAccountCreate;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Page CmsIndex
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Injection data
     *
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        CustomerAccountCreate $customerAccountCreate,
        CustomerAccountLogout $customerAccountLogout,
        CmsIndex $cmsIndex,
        fixtureFactory $fixtureFactory
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountCreate = $customerAccountCreate;
        $this->cmsIndex = $cmsIndex;
        //Precondition
        $customerInjectable = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customerInjectable->persist();
        return [
            'customerInjectable' => $customerInjectable,
        ];
    }

    /**
     * Create Existing Customer account on frontend
     *
     * @param CustomerInjectable $customerInjectable
     */
    public function testCreateExistingCustomer(CustomerInjectable $customerInjectable)
    {
        //Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Register');
        $this->customerAccountCreate->getRegisterForm()->registerCustomer($customerInjectable);
    }

    /**
     * Logout customer from frontend account
     *
     * return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
