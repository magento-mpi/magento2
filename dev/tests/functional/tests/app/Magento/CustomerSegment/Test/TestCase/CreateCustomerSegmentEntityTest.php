<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\TestCase;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Mtf\TestCase\Injectable;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Magento\CustomerSegment\Test\Constraint\AssertCustomerSegmentSuccessSaveMessage;
use Mtf\Fixture\FixtureFactory;

/**
 * Test Creation for CreateCustomerSegmentEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Test customer is created
 * 2. Test simple product is created
 *
 * Steps:
 * 1. Login to backend as admin
 * 2. Navigate to CUSTOMERS->Segment
 * 3. Click 'Add Segment' button
 * 4. Fill all fields according to dataSet and click 'Save and Continue Edit' button
 * 5. Navigate to Conditions tab
 * 6. Add specific test condition according to dataSet
 * 7. Navigate to MARKETING->Cart Price Rule and click "+"
 * 8. Fill all required data according to dataSet and save rule
 * 9. Perform assertions
 *
 * @group Customer_Segments_(CS)
 * @ZephyrId MAGETWO-25691
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreateCustomerSegmentEntityTest extends Injectable
{
    /**
     * Page promo Quote Index
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Page promo Quote Edit
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * Customer segment index page
     *
     * @var CustomerSegmentIndex
     */
    protected $customerSegmentIndex;

    /**
     * Page of create new customer segment
     *
     * @var CustomerSegmentNew
     */
    protected $customerSegmentNew;

    /**
     * Customer grid page
     *
     * @var CustomerIndex
     */
    protected $customerIndexPage;

    /**
     * Customer edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEditPage;

    /**
     * Index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Fixture sales rule
     *
     * @var SalesRuleInjectable
     */
    protected $salesRule;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Inject pages
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @param CustomerIndex $customerIndexPage
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @param CustomerIndexEdit $customerIndexEditPage
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew,
        CustomerIndex $customerIndexPage,
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        CustomerIndexEdit $customerIndexEditPage,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->customerSegmentIndex = $customerSegmentIndex;
        $this->customerSegmentNew = $customerSegmentNew;
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
        $this->customerIndexPage = $customerIndexPage;
        $this->customerIndexEditPage = $customerIndexEditPage;
        $this->cmsIndex = $cmsIndex;
        $this->fixtureFactory = $fixtureFactory;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Update customer. Create customer segment. Create Cart Price Rule
     *
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     * @param CustomerSegment $customerSegment
     * @param CustomerSegment $customerSegmentConditions
     * @param array $salesRule
     * @param AssertCustomerSegmentSuccessSaveMessage $assertCustomerSegmentSuccessSaveMessage
     * @return void
     */
    public function test(
        CustomerInjectable $customer,
        AddressInjectable $address,
        CustomerSegment $customerSegment,
        CustomerSegment $customerSegmentConditions,
        array $salesRule,
        AssertCustomerSegmentSuccessSaveMessage $assertCustomerSegmentSuccessSaveMessage
    ) {
        $this->markTestIncomplete('MAGETWO-30226');
        //Preconditions
        $customer->persist();
        $filter = ['email' => $customer->getEmail()];
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->searchAndOpen($filter);
        $this->customerIndexEditPage->getCustomerForm()->updateCustomer($customer, $address);
        $this->customerIndexEditPage->getPageActionsBlock()->save();

        //Prepare data
        $replace = [
            'conditions' => [
                '%email%' => $customer->getEmail(),
                '%company%' => $address->getCompany(),
                '%address%' => $address->getStreet(),
                '%telephone%' => $address->getTelephone(),
                '%postcode%' => $address->getPostcode(),
                '%province%' => $address->getRegionId(),
                '%city%' => $address->getCity(),
            ]
        ];

        //Steps
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getPageActionsBlock()->addNew();
        $this->customerSegmentNew->getCustomerSegmentForm()->fill($customerSegment);
        $this->customerSegmentNew->getPageMainActions()->saveAndContinue();
        $this->customerSegmentNew->getCustomerSegmentForm()->openTab('conditions');
        $this->customerSegmentNew->getCustomerSegmentForm()->fill($customerSegmentConditions, null, $replace);
        $this->customerSegmentNew->getPageMainActions()->save();

        \PHPUnit_Framework_Assert::assertThat($this->getName(), $assertCustomerSegmentSuccessSaveMessage);
        $this->createCartPriceRule($salesRule, $customerSegment);
    }

    /**
     * Create catalog price rule
     *
     * @param array $salesRule
     * @param CustomerSegment $customerSegment
     * @return void
     */
    protected function createCartPriceRule($salesRule, CustomerSegment $customerSegment)
    {
        $salesRule['conditions_serialized'] =
            str_replace('%customerSegmentName%', $customerSegment->getName(), $salesRule['conditions_serialized']);

        $this->salesRule = $this->fixtureFactory->createByCode('salesRuleInjectable', ['data' => $salesRule]);
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getGridPageActions()->addNew();
        $this->promoQuoteEdit->getSalesRuleForm()->fill($this->salesRule);
        $this->promoQuoteEdit->getFormPageActions()->save();
    }

    /**
     * Deleting cart price rule. Logout
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->salesRule instanceof SalesRuleInjectable) {
            return;
        }
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $this->salesRule->getName()]);
        $this->promoQuoteEdit->getFormPageActions()->delete();
        $this->customerAccountLogout->open();
    }
}
