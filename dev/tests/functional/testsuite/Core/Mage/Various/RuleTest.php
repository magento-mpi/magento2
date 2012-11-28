<?php
    /**
     * Magento
     *
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Various
     * @subpackage  functional_tests
     * @copyright   {copyright}
     * @license     {license_link}
     *
     */

class Core_Mage_Various_RuleTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-2244:</p>
     * <p>Weight attribute cannot be used in Shopping Cart/Catalog Price Rule condition</p>
     *
     * @param string $ruleType
     * @dataProvider ruleTypesDataProvider
     * @test
     * @TestlinkId TL-MAGE-6213
     */
    public function weightAttributeInRules($ruleType)
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute(array('attribute_code' => 'weight'));
        $this->fillDropdown('use_for_promo_rule_conditions', 'Yes');
        $this->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate($ruleType);
        $this->clickButton('add_new_rule');
        //Verifying
        $this->assertTrue($this->controlIsPresent('pageelement', 'weight_attribute'),
            "There is no Weight attribute in " . strstr($ruleType, '_'));
    }

    public function ruleTypesDataProvider()
    {
        return array(
            array('manage_catalog_price_rules'),
            array('manage_shopping_cart_price_rules'),
        );
    }
}