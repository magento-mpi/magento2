<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\TestStep;

use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Mtf\TestStep\TestStepInterface;

/**
 * Class DeleteAllSalesRuleStep
 * Delete all Sales Rule on backend
 */
class DeleteAllSalesRuleStep implements TestStepInterface
{
    /**
     * Promo Quote index page
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Promo Quote edit page
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * @construct
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     */
    public function __construct(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit
    ) {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
    }

    /**
     * Delete Sales Rule on backend
     *
     * @return array
     */
    public function run()
    {
        $this->promoQuoteIndex->open();
        while ($this->promoQuoteIndex->getPromoQuoteGrid()->isFirstRowVisible()) {
            $this->promoQuoteIndex->getPromoQuoteGrid()->openFirstRow();
            $this->promoQuoteEdit->getFormPageActions()->delete();
        }
    }
}
