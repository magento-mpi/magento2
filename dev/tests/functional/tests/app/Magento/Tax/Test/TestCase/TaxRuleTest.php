<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Tax\Test\Fixture\TaxRule;

/**
 * Class TaxRuleTest
 * Functional test for Tax Rule configuration
 *
 * @package Magento\Tax\Test\TestCase
 */
class TaxRuleTest extends Functional
{
    /**
     * Create Tax Rule with new and existing Tax Rate, Customer Tax Class, Product Tax Class
     *
     * @ZephyrId MAGETWO-12438
     */
    public function testCreateTaxRule()
    {
        //Data
        $fixture = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $fixture->switchData('us_ca_ny_rule');
        //Pages
        $taxGridPage = Factory::getPageFactory()->getTaxRule();
        $newTaxRulePage = Factory::getPageFactory()->getTaxRuleNew();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $taxGridPage->open();
        $taxGridPage->getRuleGrid()->addNewRule();
        $newTaxRulePage->getEditBlock()->fill($fixture);
        $newTaxRulePage->getEditBlock()->clickSaveAndContinue();
        //Verifying
        $newTaxRulePage->getMessagesBlock()->assertSuccessMessage();
        $this->_assertOnGrid($fixture);
    }

    /**
     * Assert existing tax rule on manage tax rule grid
     *
     * @param TaxRule $fixture
     */
    protected function _assertOnGrid(TaxRule $fixture)
    {
        //Data
        $taxRates = array();
        foreach ($fixture->getTaxRate() as $rate) {
            $taxRates[] = $rate['code']['value'];
        }
        $filter = array(
            'name' => $fixture->getTaxRuleName(),
            'customer_tax_class' => implode(', ', $fixture->getTaxClass('customer')),
            'product_tax_class' => implode(', ', $fixture->getTaxClass('product')),
            'tax_rate' => implode(', ', $taxRates)
        );
        //Verification
        $taxGridPage = Factory::getPageFactory()->getTaxRule();
        $taxGridPage->open();
        $this->assertTrue($taxGridPage->getRuleGrid()->isRowVisible($filter), 'New tax rule was not found.');
    }
}
