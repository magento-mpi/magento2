<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentNew;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Test Creation for UpdateCustomerSegmentEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Delete all existed customers.
 * 2. Test customer segment is created according to specified predefined dataset.
 * 3. Test customer is created according to specified predefined dataset.
 * 4. Test simple product is created.
 * 5. CartPriceRule which contains in conditions created customer segment is created.
 *
 * Steps:
 * 1. Login to backend as admin.
 * 2. Use the main menu "CUSTOMERS"->"Segments".
 * 3. Search and open created segment.
 * 4. Fill all fields according to dataSet and click 'Save and Continue Edit' button.
 * 5. Navigate to Conditions tab.
 * 6. Click the "Remove" button for a original conditions.
 * 7. Perform assertions.
 *
 * @group Customer_Segments_(CS)
 * @ZephyrId MAGETWO-26420
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateCustomerSegmentEntityTest extends Injectable
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
    protected $customerEditPage;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

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
     * Inject pages
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @param CustomerSegmentNew $customerSegmentNew
     * @param CustomerIndex $customerIndexPage
     * @param CustomerAccountLogout $customerAccountLogout
     * @param FixtureFactory $fixtureFactory
     * @param CustomerIndexEdit $customerEditPage
     * @return void
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        CustomerSegmentIndex $customerSegmentIndex,
        CustomerSegmentNew $customerSegmentNew,
        CustomerIndex $customerIndexPage,
        CustomerAccountLogout $customerAccountLogout,
        FixtureFactory $fixtureFactory,
        CustomerIndexEdit $customerEditPage
    ) {
        $this->customerSegmentIndex = $customerSegmentIndex;
        $this->customerSegmentNew = $customerSegmentNew;
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
        $this->customerIndexPage = $customerIndexPage;
        $this->customerEditPage = $customerEditPage;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->fixtureFactory = $fixtureFactory;
        $customerIndexPage->open();
        $customerIndexPage->getCustomerGridBlock()->massaction([], 'Delete', true, 'Select All');
    }

    /**
     * Update customer. Create customer segment. Create Cart Price Rule
     *
     * @param CustomerInjectable $customer
     * @param AddressInjectable $address
     * @param CustomerSegment $customerSegment
     * @param CustomerSegment $customerSegmentOriginal
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        AddressInjectable $address,
        CustomerSegment $customerSegment,
        CustomerSegment $customerSegmentOriginal
    ) {
        $this->markTestIncomplete('MAGETWO-30226');
        //Preconditions
        $customer->persist();
        $this->customerIndexPage->open();
        $this->customerIndexPage->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $this->customerEditPage->getCustomerForm()->updateCustomer($customer, $address);
        $this->customerEditPage->getPageActionsBlock()->save();
        $customerSegmentOriginal->persist();

        $conditions = '[Customer Segment|matches|' . $customerSegmentOriginal->getSegmentId() . ']';
        $this->salesRule = $this->fixtureFactory->createByCode(
            'salesRuleInjectable',
            [
                'dataSet' => 'active_sales_rule_for_retailer',
                'data' => ['conditions_serialized' => $conditions],
            ]
        );
        $this->salesRule->persist();

        //Steps
        $this->customerSegmentIndex->open();
        $this->customerSegmentIndex->getGrid()->searchAndOpen(
            [
                'grid_segment_name' => $customerSegmentOriginal->getName(),
            ]
        );
        $this->customerSegmentNew->getCustomerSegmentForm()->fill($customerSegment);
        $this->customerSegmentNew->getPageMainActions()->save();

        return ['customerSegment' => $this->mergeFixture($customerSegment, $customerSegmentOriginal)];
    }

    /**
     * Merge Customer Segment fixtures
     *
     * @param CustomerSegment $segment
     * @param CustomerSegment $segmentOriginal
     * @return CustomerSegment
     */
    protected function mergeFixture(CustomerSegment $segment, CustomerSegment $segmentOriginal)
    {
        $data = array_merge($segmentOriginal->getData(), $segment->getData());
        return $this->fixtureFactory->createByCode('customerSegment', ['data' => $data]);
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
        $this->customerAccountLogout->open();
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $this->salesRule->getName()]);
        $this->promoQuoteEdit->getFormPageActions()->delete();
    }
}
