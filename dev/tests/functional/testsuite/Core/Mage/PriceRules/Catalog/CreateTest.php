<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Price Rule creation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_Catalog_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_catalog_price_rules');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_catalog_price_rules');
        $this->priceRulesHelper()->deleteAllRules();
        $this->clickButton('apply_rules', false);
        $this->waitForNewPage();
        $this->assertMessagePresent('success', 'success_applied_rule');
    }

    /**
     * <p>Create a new catalog price rule</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-3313
     */
    public function requiredFields()
    {
        //Data
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        return $priceRuleData;
    }

    /**
     * <p>Validation of empty required fields</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-3309
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule', array($emptyField => '%noValue%'));
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('rule_name', 'field'),
            array('customer_groups', 'multiselect'),
            array('discount_amount', 'field'), //MAGE-5623(reproduce in 1.6.2,but is not reproducible in nightly build)
            array('sub_discount_amount', 'field')
        );
    }

    /**
     * <p>Validation of Discount Amount field</p>
     *
     * @param string $invalidDiscountData
     *
     * @test
     * @dataProvider invalidDiscountAmountDataProvider
     * @TestlinkId TL-MAGE-3311
     */
    public function invalidDiscountAmount($invalidDiscountData)
    {
        //Data
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule',
                                            array('sub_discount_amount' => $invalidDiscountData,
                                                 'discount_amount'      => $invalidDiscountData));
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->addFieldIdToMessage('field', 'discount_amount');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->addFieldIdToMessage('field', 'sub_discount_amount');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    public function invalidDiscountAmountDataProvider()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJTest'),
            array('-128')
        );
    }

    /**
     * <p>Create Catalog price rule with long values into required fields.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3312
     */
    public function longValues()
    {
        $this->markTestIncomplete('MAGETWO-1682');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule',
                                            array('rule_name'          => $this->generate('string', 255, ':alnum:'),
                                                 'description'         => $this->generate('string', 255, ':alnum:'),
                                                 'discount_amount'     => '99999999.9999',
                                                 'sub_discount_amount' => '99999999.9999',
                                                 'priority'            => '4294967295'));
        $ruleSearch = $this->loadDataSet('CatalogPriceRule', 'search_catalog_rule',
                                         array('filter_rule_name' => $priceRuleData['info']['rule_name']));
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->openRule($ruleSearch);
        $this->priceRulesHelper()->verifyRuleData($priceRuleData);
    }

    /**
     * <p>Create Catalog price rule with long values into required fields.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3310
     */
    public function incorrectLengthInDiscountAmount()
    {
        $this->markTestIncomplete('MAGETWO-1682');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule',
                                            array('discount_amount'    => '999999999',
                                                 'sub_discount_amount' => '999999999'));
        $ruleSearch = $this->loadDataSet('CatalogPriceRule', 'search_catalog_rule',
                                         array('filter_rule_name' => $priceRuleData['info']['rule_name']));
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->openRule($ruleSearch);
        $priceRuleData['actions']['discount_amount'] = '99999999.9999';
        $priceRuleData['actions']['sub_discount_amount'] = '99999999.9999';
        $this->priceRulesHelper()->verifyRuleData($priceRuleData);
    }
}