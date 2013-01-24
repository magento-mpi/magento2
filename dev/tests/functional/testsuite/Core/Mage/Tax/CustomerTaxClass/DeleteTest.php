<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Tax class Core_Mage_deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_CustomerTaxClass_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Save rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Manage Tax Rules</p>
     * <p>Click on the Add New Rule button </p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_rule');
    }

    protected function tearDownAfterTest()
    {
        //Remove Tax rule after test
        if (!empty($this->_ruleToBeDeleted)) {
            $this->loginAdminUser();
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->_ruleToBeDeleted, 'rule');
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
     * <p>Delete existing Customer Tax class</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6392
     */
    public function deleteNotUsedInRule()
    {
        $multiselect = 'customer_tax_class';
        $taxClassName = $this->generate('string', 20);
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->fillCompositeMultiselect('customer_tax_class', array($taxClassName));
        $this->assertTrue($this->verifyCompositeMultiselect('customer_tax_class', array($taxClassName)),
            $this->getParsedMessages());
        $this->deleteCompositeMultiselectOption($multiselect, $taxClassName, 'confirmation_for_delete_class');
    }

    /**
     * <p>Delete a Customer Tax class that used</p>
     *
     * @test
     * @depends deleteNotUsedInRule
     * @TestlinkId TL-MAGE-6393
     */
    public function usedInRule()
    {
        $taxClass = $this->generate('string', 30);
        $multiselect = 'customer_tax_class';
        //Create Tax Class
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->addCompositeMultiselectValue($multiselect, $taxClass);
        //Create Tax Rule
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('customer_tax_class' => $taxClass));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $this->loadDataSet('Tax', 'search_tax_rule',
            array('filter_name' => $taxRuleData['name']));
        $this->taxHelper()->deleteTaxClass($taxClass, 'customer_tax_class', 'used_in_rule_error');
    }

    /**
     * <p>Delete a Customer Tax class Core_Mage_that used in Customer Group</p>
     *
     * @depends deleteNotUsedInRule
     * @test
     */
    public function usedInCustomerGroup()
    {
        $taxClass = $this->generate('string', 20);
        $customerGroupData = $this->loadDataSet('CustomerGroup', 'new_customer_group',
            array('tax_class' => $taxClass));
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->addCompositeMultiselectValue($multiselect, $taxClass);
        $this->navigate('manage_customer_groups');
        $this->customerGroupsHelper()->createCustomerGroup($customerGroupData);
        $this->assertMessagePresent('success', 'success_saved_customer_group');
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->deleteTaxClass($taxClass, 'customer_tax_class', 'used_in_customer_group_error');
    }
}