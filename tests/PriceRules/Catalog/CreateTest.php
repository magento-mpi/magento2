<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Price Rule creation
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_Catalog_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Promotions -> Catalog Price Rules</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_catalog_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_catalog_price_rules'), $this->messages);
    }

    /**
     * <p>Create a new catalog price rule</p>
     *
     * <p>Steps</p>
     * <p>1. Click "Add New Rule"</p>
     * <p>2. Fill in only required fields in all tabs</p>
     * <p>3. Click "Save Rule" button</p>
     *
     * <p>Expected result:</p>
     * <p>New rule is created. Success message appears.</p>
     *
     * @test
     */
    public function createCatalogPriceRuleRequiredFields()
    {
        //Data
        $priceRuleData = $this->loadData('test_catalog_rule', array('customer_groups' => 'General'), 'rule_name');
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->successMessage('notification_message'), $this->messages);
        $this->search(array('filter_rule_name' => $priceRuleData['info']['rule_name']));
        return $priceRuleData['info']['rule_name'];
    }

    /**
     * <p>Validation of empty required fields</p>
     *
     * <p>Steps</p>
     * <p>1. Click "Add New Rule"</p>
     * <p>2. Leave required fields empty</p>
     * <p>3. Click "Save Rule" button</p>
     *
     * <p>Expected result: Validation message appears</p>
     * <p>https://jira.magento.com/browse/MAGE-4870</p>
     *
     * @dataProvider dataEmptyField
     * @test
     */
    public function createCatalogPriceRuleEmptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $priceRuleData = $this->loadData('test_catalog_rule', array($emptyField => '%noValue%'), 'rule_name');
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyField()
    {
        return array(
            array('rule_name', 'field'),
            array('customer_groups', 'multiselect'),
            array('discount_amount', 'field'),
            array('sub_discount_amount', 'field')
        );
    }

    /**
     * <p>Validation of Discount Amount field</p>
     *
     * <p>Steps</p>
     * <p>1. Click "Add New Rule"</p>
     * <p>2. Fill in "General Information" tab</p>
     * <p>3. Specify "Conditions"</p>
     * <p>4. Enter invalid data into "Discount Amount" and "Sub Discount Amount" fields</p>
     *
     * <p>Expected result: Validation messages appears</p>
     *
     * @dataProvider dataInvalidDiscount
     * @test
     */
    public function createCatalogPriceRuleInvalidDiscountAmount($invalidDiscountData)
    {
        //Data
        $priceRuleData = $this->loadData('test_catalog_rule',
            array('sub_discount_amount' => $invalidDiscountData,
                  'discount_amount'     => $invalidDiscountData), 'rule_name');
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->assertTrue($this->validationMessage('invalid_discount_amount'), $this->messages);
        $this->assertTrue($this->validationMessage('invalid_sub_discount_amount'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
    }

    public function dataInvalidDiscount()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJHghj'),
            array('-128')
        );
    }

    /**
     * <p>Create catalog price rule - editing created rule</p>
     *
     * <p>Steps</p>
     * <p>1. Select an existing rule from the grid and open it</p>
     * <p>2. Make some changes into the rule</p>
     * <p>3. Click "Save Rule" button</p>
     *
     * <p>Expected result:</p>
     * <p>New rule is created. Success message appears</p>
     *
     * @depends createCatalogPriceRuleRequiredFields
     * @test
     */
    public function editRule($createdRuleData)
    {
        //Data
        $editRuleData = $this->loadData('test_catalog_rule',
            array('rule_name'           => 'edited_rule_name',
                  'status'              => 'Inactive',
                  'customer_groups'     => 'General',
                  'discount_amount'     => '25',
                  'sub_discount_amount' => '35')
        );
        $ruleSearch = $this->loadData('search_catalog_rule',
            array('filter_rule_name'   => $editRuleData['info']['rule_name'], 'status' => 'Inactive'));
        //Steps
        $this->search(array('filter_rule_name' => $createdRuleData));
        $this->priceRulesHelper()->createRule($editRuleData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->priceRulesHelper()->openRule($ruleSearch);
        $this->priceRulesHelper()->verifyRuleData($editRuleData);
    }

}
