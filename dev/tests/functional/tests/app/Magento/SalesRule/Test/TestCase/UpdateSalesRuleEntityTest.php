<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\TestCase;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for UpdateSalesRuleEntity
 *
 * Test Flow:
 * Precondition:
 * 1. Cart Price Rule is created
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate to MARKETING > Cart Price Rules
 * 3. Click Cart Price Rule from grid
 * 4. Edit test value(s) according to dataset
 * 5. Click 'Save' button
 * 6. Perform asserts
 *
 * @group Shopping_Cart_Price_Rules_(CS)
 * @ZephyrId MAGETWO-24860
 */
class UpdateSalesRuleEntityTest extends Injectable
{
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
     * Create simple product with category
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $productForSalesRule1 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'simple_for_salesrule_1']
        );
        $productForSalesRule1->persist();
        return [
            'productForSalesRule1' => $productForSalesRule1,
        ];
    }

    /**
     * Inject data
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @return void
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Update Sales Rule Entity
     *
     * @param SalesRuleInjectable $salesRule
     * @param SalesRuleInjectable $salesRuleOrigin
     * @param AddressInjectable $address
     * @param array $productQuantity
     * @param array $shipping
     * @param int|null $isLoggedIn
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testUpdateSalesRule(
        SalesRuleInjectable $salesRule,
        SalesRuleInjectable $salesRuleOrigin,
        AddressInjectable $address,
        array $productQuantity,
        array $shipping,
        $isLoggedIn = null
    ) {
        // Preconditions
        $salesRuleOrigin->persist();
        $filter = [
            'name' => $salesRuleOrigin->getName(),
        ];
        $this->salesRuleName = $salesRule->hasData('name') ? $salesRule->getName() : $salesRuleOrigin->getName();

        // Steps
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
        $this->promoQuoteEdit->getSalesRuleForm()->fill($salesRule);
        $this->promoQuoteEdit->getFormPageActions()->save();
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
