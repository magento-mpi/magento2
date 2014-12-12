<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\TestCase;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create SalesRuleEntity
 *
 * Test Flow:
 * Precondition:
 * 1. 2 sub categories in Default Category are created.
 * 2. 2 simple products are created and assigned to different subcategories by one for each
 * 3. Default customer are created
 *
 * Steps:
 * 1. Login to backend as admin
 * 2. Navigate to MARKETING->Cart Price Rule
 * 3. Create Cart Price rule according to dataset and click "Save" button
 * 4. Perform asserts
 *
 * @group Shopping_Cart_Price_Rules_(CS)
 * @ZephyrId MAGETWO-24855
 */
class CreateSalesRuleEntityTest extends Injectable
{
    /**
     * Page PromoQuoteNew
     *
     * @var PromoQuoteNew
     */
    protected $promoQuoteNew;

    /**
     * Page PromoQuoteEdit
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * Page PromoQuoteIndex
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Sales rule name
     *
     * @var string
     */
    protected $salesRuleName;

    /**
     * Inject data
     *
     * @param PromoQuoteNew $promoQuoteNew
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @return void
     */
    public function __inject(
        PromoQuoteNew $promoQuoteNew,
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->promoQuoteNew = $promoQuoteNew;
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Create customer and 2 simple products with categories before run test
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        $productForSalesRule1 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'simple_for_salesrule_1']
        );
        $productForSalesRule1->persist();

        $productForSalesRule2 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'simple_for_salesrule_2']
        );
        $productForSalesRule2->persist();

        return [
            'customer' => $customer,
            'productForSalesRule1' => $productForSalesRule1,
            'productForSalesRule2' => $productForSalesRule2
        ];
    }

    /**
     * Create Sales Rule Entity
     *
     * @param SalesRuleInjectable $salesRule
     * @param AddressInjectable $address
     * @param array $productQuantity
     * @param array $shipping
     * @param int $isLoggedIn
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testCreateSalesRule(
        SalesRuleInjectable $salesRule,
        AddressInjectable $address,
        $productQuantity,
        $shipping,
        $isLoggedIn
    ) {
        // Preconditions
        $this->salesRuleName = $salesRule->getName();

        // Steps
        $this->promoQuoteNew->open();
        $this->promoQuoteNew->getSalesRuleForm()->fill($salesRule);
        $this->promoQuoteNew->getFormPageActions()->save();
    }

    /**
     * Delete current sales rule
     *
     * @return void
     */
    public function tearDown()
    {
        $filter = [
            'name' => $this->salesRuleName,
        ];

        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
        $this->promoQuoteEdit->getFormPageActions()->delete();
    }
}
