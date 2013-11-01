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

class TaxRuleTest extends Functional
{
    public function testCreateTaxRule()
    {
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
        /** @var \Magento\Backend\Test\Block\Tax\Rule */
        $taxGridBlock = $taxGridPage->getRuleGrid();

        $data = $fixture->getData('fields');
        $customerClasses = $data['tax_customer_class'];
        $productClasses = $data['tax_product_class'];
        $rates = $data['tax_rate'];

        $configurableSearch = array(
            0 => array(
                'name' => $data['code']['value'],
                'customer_tax_class' => $customerClasses[0]['value'],
                'product_tax_class' => $productClasses[0]['value'],
                'tax_rate' => $rates[0]['code']['value']
                ),
            1 => array(
                'name' => $data['code']['value'],
                'customer_tax_class' => $customerClasses[1]['value'],
                'product_tax_class' => $productClasses[1]['value'],
                'tax_rate' => $rates[1]['code']['value']
            )
        );
        //resetfilter
        $taxGridBlock->resetFilter();
        //fillFilter
        $taxGridBlock->prepareForSearch($configurableSearch[0]);
        //Click Search
        $taxGridBlock->clickSearchButton();
        //Assert search result
        //Assertion
//        $this->assertTrue($taxGridBlock->isRowVisible($configurableSearch[0]), 'New tax rule was not found.');
//        $this->assertTrue($taxGridBlock->isRowVisible($configurableSearch[1]), 'New tax rule was not found.');

    }
}
