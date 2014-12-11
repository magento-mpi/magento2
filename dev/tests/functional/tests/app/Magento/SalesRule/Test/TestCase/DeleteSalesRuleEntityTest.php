<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\TestCase;

use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteSalesRuleEntity
 *
 * Test Flow:
 * Precondition:
 * 1. Several Shopping Cart Price Rules are created
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate to MARKETING > Cart Price Rules
 * 3. Open from grid test Rule
 * 4. Click 'Delete' button
 * 5. Perform asserts
 *
 * @group Shopping_Cart_Price_Rules_(CS)
 * @ZephyrId MAGETWO-24985
 */
class DeleteSalesRuleEntityTest extends Injectable
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
     * Inject data
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Delete Sales Rule Entity
     *
     * @param SalesRuleInjectable $salesRule
     * @return void
     */
    public function testDeleteSalesRule(SalesRuleInjectable $salesRule)
    {
        // Preconditions
        $salesRule->persist();
        $filter = [
            'name' => $salesRule->getName(),
        ];

        // Steps
        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
        $this->promoQuoteEdit->getFormPageActions()->delete();
    }
}
