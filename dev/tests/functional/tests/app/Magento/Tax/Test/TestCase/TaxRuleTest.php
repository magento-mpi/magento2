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

/**
 * Functional test for Tax Rule configuration
 */
class TaxRuleTest extends Functional
{
    /**
     * Test case for new Tax Rule creation
     */
    public function testCreateTaxRule()
    {
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $fixture = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $fixture->switchData('us_ca_ny_rule');

        Factory::getApp()->magentoBackendLoginUser();
        $taxGridPage = Factory::getPageFactory()->getAdminTaxRule();
        $taxGridPage->open();
        $taxGridPage->getActionsBlock()->clickAddNew();
        $newTaxRulePage = Factory::getPageFactory()->getAdminTaxRuleNew();
        $newTaxRulePage->open();
        $newTaxRulePage->getEditBlock()->createTaxRule($fixture);
        $taxGridPage->open();

        $this->assertTrue($taxGridPage->getRuleGrid()->isRowVisible(array($fixture->getTaxRuleName())), 'New tax rule was not found.');
    }
}
