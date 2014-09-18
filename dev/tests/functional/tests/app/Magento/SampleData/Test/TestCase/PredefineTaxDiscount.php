<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SampleData\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Tax\Test\Fixture\TaxRule;
use Magento\Core\Test\Fixture\ConfigData;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

/**
 * Class PredefineTaxDiscount
 * Predefine tax and discount data
 *
 * @ticketId MTA-404
 */
class PredefineTaxDiscount extends Injectable
{
    /**
     * Predefine tax and discount data
     *
     * @param TaxRule $tax
     * @param SalesRuleInjectable $cartPriceRule
     * @param CatalogRule $catalogRule
     * @param ConfigData $store
     * @return void
     */
    public function test(TaxRule $tax, SalesRuleInjectable $cartPriceRule, CatalogRule $catalogRule, ConfigData $store)
    {
        $tax->persist();
        $cartPriceRule->persist();
        $catalogRule->persist();
        $store->persist();
    }
}
