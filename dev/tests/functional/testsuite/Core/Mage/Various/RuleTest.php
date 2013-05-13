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
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute(array('attribute_code' => 'weight'));
        $this->fillDropdown('use_for_promo_rule_conditions', 'Yes');
        $this->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-2244:</p>
     * <p>Weight attribute cannot be used in Shopping Cart Price Rule condition</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6213
     */
    public function weightAttributeInShoppingCartPriceRule()
    {
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        $this->addParameter('condition', 1);
        $this->addParameter('key', 1);
        $this->clickControl('link', 'condition_new_child', false);
        $this->fillDropdown('select_condition_new_child', 'Product attribute combination');
        $this->addParameter('condition', '1--1');
        $this->clickControl('link', 'condition_new_child', false);
        $conditions = $this->select($this->getControlElement('dropdown', 'select_condition_new_child'))
            ->selectOptionLabels();
        //Verifying
        $this->assertTrue(in_array('Weight', $conditions), "There is no Weight attribute in Shopping Cart Price Rule");
    }

    /**
     * <p>Bug Cover<p/>
     * <p>Verification of MAGETWO-2244:</p>
     * <p>Weight attribute cannot be used in Catalog Price Rule condition</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6213
     */
    public function weightAttributeInCatalogPriceRule()
    {
        //Steps
        $this->navigate('manage_catalog_price_rules');
        $this->clickButton('add_new_rule');
        $this->openTab('rule_conditions');
        $this->addParameter('condition', 1);
        $this->addParameter('key', 1);
        $this->clickControl('link', 'condition_new_child', false);
        $conditions = $this->select($this->getControlElement('dropdown', 'select_condition_new_child'))
            ->selectOptionLabels();
        //Verifying
        $this->assertTrue(in_array('Weight', $conditions), "There is no Weight attribute in Catalog Price Rule");
    }
}