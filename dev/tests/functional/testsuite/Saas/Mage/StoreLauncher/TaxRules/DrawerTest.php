<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Rules Drawer tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_StoreLauncher_TaxRules_DrawerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Save rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Store Launcher page</p>
     * <p>3. Reset tile state</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->storeLauncherHelper()->resetTaxTile();
        //Back to admin
        $this->loginAdminUser();
    }

    /**
     * <p>Remove Tax Rule</p>
     */
    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        if (!empty($this->_ruleToBeDeleted)) {
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->_ruleToBeDeleted, 'rule');
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
     * <p>Disable tax rules</p>
     *
     * @test
     */
    public function disableTaxRules()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->assertTrue($this->controlIsPresent('button', 'tax_rules_switcher'), 'Tax Rules could not be disabled');
        $this->clickButton('tax_rules_switcher', false);
        $this->assertTrue(
            $this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'no_tax_title'), 'Tax Rules are not disabled');

        $this->clickButton('tax_rules_switcher', false);
        $this->assertFalse(
            $this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'no_tax_title'), 'Tax Rules are not enabled');

        $this->clickButton('tax_rules_switcher', false);
        $this->storeLauncherHelper()->saveDrawer();
        $this->assertEquals('tile-store-settings tile-tax tile-complete',
            $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'tax_rules_tile', 'class'),
            'Tile state is not Equal to Complete'
        );
        $tileElement = $this->storeLauncherHelper()->mouseOverDrawer('tax_rules_tile');
        $action = $this->getChildElement($tileElement,
            $this->_getControlXpath(self::FIELD_TYPE_LINK, 'additional_action'));
        $this->assertNotNull($action, 'Could not find "Additional action link"');
        $action->click();
        $this->waitForPageToLoad();
        $this->validatePage('manage_tax_rule');
    }

    /**
     * <p>Handle new Tax Rule. Tile should change state to Complete</p>
     *
     * @test
     */
    public function tileStateOnAddTaxRule()
    {
        $this->assertEquals('tile-store-settings tile-tax tile-todo',
            $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'tax_rules_tile', 'class'),
            'Tile state is not Equal to TODO');
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required');
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxRule($taxRuleData);
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $searchTaxRuleData;
        $this->admin();
        $this->assertEquals('tile-store-settings tile-tax tile-complete',
            $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'tax_rules_tile', 'class'),
            'Tile state in Equal to Complete');
    }

    //************************* Tax Rate **********************************************
    /**
     * Add new Tax Rate on Drawer
     *
     * @param $taxRateDataSetName
     * @dataProvider withRequiredFieldsOnlyDataProvider
     * @test
     */
    public function createTaxRateRequiredFieldsOnly($taxRateDataSetName)
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $rate = $this->loadDataSet('Tax', $taxRateDataSetName);
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $rate['tax_identifier']));
        $this->taxRuleHelper()->createTaxRate($rate);
        $this->assertTrue($this->verifyCompositeMultiselect('tax_rate', array($rate['tax_identifier'])),
            $this->getParsedMessages());
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->openTaxItem($search, 'rate');
        $this->assertTrue($this->verifyForm($rate), $this->getParsedMessages());
    }

    /**
     * Data for createTaxRateRequiredFieldsOnly
     *
     * @return array
     */
    public function withRequiredFieldsOnlyDataProvider()
    {
        return array(
            array('tax_rate_create_test_zip_no'), // Zip/Post is Range => No
            array('tax_rate_create_test_zip_yes') // Zip/Post is Range => Yes
        );
    }

    /**
     * <p>Edit Tax Rate</p>
     *
     * @test
     * @depends createTaxRateRequiredFieldsOnly
     */
    public function editExistingTaxRate()
    {
        $rate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->taxRuleHelper()->createTaxRate($rate);
        $this->assertTrue($this->verifyCompositeMultiselect('tax_rate', array($rate['tax_identifier'])),
            $this->getParsedMessages());
        $newRate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_yes');
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $newRate['tax_identifier']));
        $this->taxRuleHelper()->editTaxRate($rate['tax_identifier'], $newRate);
        $this->assertTrue($this->verifyCompositeMultiselect('tax_rate', array($newRate['tax_identifier'])),
            $this->getParsedMessages());
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->openTaxItem($search, 'rate');
        $this->assertTrue($this->verifyForm($newRate), $this->getParsedMessages());
    }

    /**
     * <p>Delete a Tax Rate</p>
     *
     * @depends createTaxRateRequiredFieldsOnly
     * @test
     */
    public function deleteTaxRate()
    {
        $rate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->taxRuleHelper()->createTaxRate($rate);
        $this->assertTrue($this->verifyCompositeMultiselect('tax_rate', array($rate['tax_identifier'])),
            $this->getParsedMessages());
        $this->deleteCompositeMultiselectOption('tax_rate', $rate['tax_identifier'], 'confirmation_for_delete_rate');
    }

    //************************* Customer Tax Class ********************************
    /**
     * <p>Add new Customer Tax Class on Drawer</p>
     *
     * @test
     */
    public function createCustomerTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_customer_tax_class');
        $this->fillCompositeMultiselect('customer_tax_class', array($taxClass['customer_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('customer_tax_class',
                array($taxClass['customer_tax_class'])), 'Failed to add new value');
    }

    /**
     * <p>Rename Customer Tax Class</p>
     *
     * @depends createCustomerTaxClass
     * @test
     */
    public function editExistingCustomerTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_customer_tax_class');
        $this->fillCompositeMultiselect('customer_tax_class', array($taxClass['customer_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('customer_tax_class',
                array($taxClass['customer_tax_class'])), 'Failed to add new value');
        $newTaxName = $this->generate('string', 10);
        $this->editCompositeMultiselectOption('customer_tax_class', $taxClass['customer_tax_class'], $newTaxName);
        $this->assertTrue($this->verifyCompositeMultiselect('customer_tax_class', $newTaxName));
    }

    /**
     * <p>Delete a Customer Tax Class</p>
     *
     * @depends createCustomerTaxClass
     * @test
     */
    public function deleteCustomerTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_customer_tax_class');
        $this->fillCompositeMultiselect('customer_tax_class', array($taxClass['customer_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('customer_tax_class',
                array($taxClass['customer_tax_class'])), 'Failed to add new value');
        $this->deleteCompositeMultiselectOption(
            'customer_tax_class', $taxClass['customer_tax_class'], 'confirmation_for_delete_class');
    }

    //************************* Product Tax Class ********************************
    /**
     * <p>Add new Product Tax Class on Drawer</p>
     *
     * @test
     */
    public function createProductTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class');
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass['product_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($taxClass['product_tax_class'])),
            'Failed to add new value');
    }

    /**
     * <p>Rename Product Tax Class</p>
     *
     * @depends createProductTaxClass
     * @test
     */
    public function editExistingProductTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class');
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass['product_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class',
                array($taxClass['product_tax_class'])), 'Failed to add new value');
        $newTaxName = $this->generate('string', 10);
        $this->editCompositeMultiselectOption('product_tax_class', $taxClass['product_tax_class'], $newTaxName);
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', $newTaxName));
    }

    /**
     * <p>Delete a Product Tax Class</p>
     *
     * @depends createProductTaxClass
     * @test
     */
    public function deleteProductTaxClass()
    {
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class');
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass['product_tax_class']));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class',
                array($taxClass['product_tax_class'])), 'Failed to add new value');
        $this->deleteCompositeMultiselectOption(
            'product_tax_class', $taxClass['product_tax_class'], 'confirmation_for_delete_class');
    }

    //************************* Tax Rule ****************************************
    /**
     * <p>Creating Tax Rule with required fields</p>
     *
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required');
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $this->fillFieldset($taxRuleData, 'tax_rules_drawer');
        $this->storeLauncherHelper()->saveDrawer();
        $this->_ruleToBeDeleted = $searchTaxRuleData;
        $this->assertEquals('tile-store-settings tile-tax tile-complete',
            $this->getControlAttribute(self::UIMAP_TYPE_FIELDSET, 'tax_rules_tile', 'class'), 'Tile state is not Equal to Complete');
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link');
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
    }

    /**
     * <p>Creating a Tax Rule with empty required fields.</p>
     *
     * @test
     * @param string $emptyFieldName Name of the field to leave empty
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends withRequiredFieldsOnly
     */
    public function withEmptyRequiredFields($emptyFieldName)
    {
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', array($emptyFieldName => ''));
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $this->fillFieldset($taxRuleData, 'tax_rules_drawer');
        $this->clickButton('save_my_settings', false);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Data for withEmptyRequiredFields
     *
     * @return array
     */
    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('name'),
            array('customer_tax_class'),
            array('product_tax_class'),
            array('tax_rate'),
            array('priority'),
            array('sort_order')
        );
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Priority.</p>
     *
     * @param string $specialValue
     * @dataProvider invalidValuesDataProvider
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withInvalidValuesForPriority($specialValue)
    {
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', array('priority' => $specialValue));
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $this->fillFieldset($taxRuleData, 'tax_rules_drawer');
        $this->clickButton('save_my_settings', false);
        $this->addFieldIdToMessage('field', 'priority');
        $this->assertMessagePresent('error', 'enter_not_negative_number');
    }

    /**
     * <p>Creating a new Tax Rule with invalid values for Sort Order.</p>
     *
     * @param string $specialValue
     * @dataProvider invalidValuesDataProvider
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withInvalidValuesForSortOrder($specialValue)
    {
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', array('sort_order' => $specialValue));
        $this->storeLauncherHelper()->openDrawer('tax_rules_tile');
        $this->clickControl(self::FIELD_TYPE_LINK, 'tax_rule_info_additional_link', false);
        $this->fillFieldset($taxRuleData, 'tax_rules_drawer');
        $this->clickButton('save_my_settings', false);
        $this->addFieldIdToMessage('field', 'sort_order');
        $this->assertMessagePresent('error', 'enter_not_negative_number');
    }

    /**
     * <p>Test data for withInvalidValuesForPriority, withInvalidValuesForSortOrder.</p>
     *
     * @return array
     */
    public function invalidValuesDataProvider()
    {
        return array(
            array($this->generate('string', 50, ':alpha:')),
            array($this->generate('string', 50, ':punct:'))
        );
    }
}