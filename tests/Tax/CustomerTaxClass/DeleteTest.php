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
 * Customer Tax Class deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_CustomerTaxClass_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Save rule name for clean up</p>
     */
    protected $ruleToBeDeleted = null;

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Customer Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_customer_tax_class');
    }

    /**
     * <p>Create Tax Rate for tests<p>
     *
     * @return array $taxRateData
     * @test
     */
    public function setupTestDataCreateTaxRate()
    {
        //Data
        $taxRateData = $this->loadData('tax_rate_create_test', null, 'tax_identifier');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRateData);
        $this->assertTrue($this->successMessage('success_saved_tax_rate'), $this->messages);
        return $taxRateData;
    }

    /**
     * <p>Delete a Customer Tax Class</p>
     * <p>Steps:</p>
     * <p>1. Create a new Customer Tax Class</p>
     * <p>2. Open the Customer Tax Class</p>
     * <p>3. Delete the Customer Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Customer Tax Class has been deleted.</p>
     *
     * @test
     */
    public function notUsedInRule()
    {
        //Data
        $customerTaxClassData = $this->loadData('new_customer_tax_class', null, 'customer_class_name');
        //Steps
        $this->taxHelper()->createCustomerTaxClass($customerTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->taxHelper()->deleteTaxItem($customerTaxClassData ,'customer_tax_class');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_tax_class'), $this->messages);
    }

    /**
     * <p>Delete a Customer Tax Class that used</p>
     * <p>Steps:</p>
     * <p>1. Create a new Customer Tax Class</p>
     * <p>2. Create a new Tax Rule that use Customer Tax Class from previous step</p>
     * <p>2. Open the Customer Tax Class</p>
     * <p>3. Delete the Customer Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Customer Tax Class could not be deleted.</p>
     *
     * @depends setupTestDataCreateTaxRate
     * @param array $taxRateData
     * @test
     */
    public function usedInRule($taxRateData)
    {
        //Data
        $customerTaxClassData = $this->loadData('new_customer_tax_class', null, 'customer_class_name');
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate' => $taxRateData['tax_identifier'],
                                            'customer_tax_class' => $customerTaxClassData['customer_class_name']),'name');
        $searchTaxRuleData = $this->loadData('search_tax_rule',
                                             array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createCustomerTaxClass($customerTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxRule($taxRuleData);
        $this->assertTrue($this->successMessage('success_saved_tax_rule'), $this->messages);
        $this->ruleToBeDeleted = $searchTaxRuleData;      //For Clean Up
        $this->navigate('manage_customer_tax_class');
        $this->taxHelper()->deleteTaxItem($customerTaxClassData ,'customer_tax_class');
        //Verifying
        $this->assertTrue($this->errorMessage('error_delete_tax_class'), $this->messages);
    }

    /**
     * <p>Delete a Customer Tax Class that used in Customer Group</p>
     * <p>Steps:</p>
     * <p>1. Create a new Customer Tax Class</p>
     * <p>2. Create a new Product that use Customer Tax Class from previous step</p>
     * <p>2. Open the Customer Tax Class</p>
     * <p>3. Delete the Customer Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Customer Tax Class could not be deleted.</p>
     * <p>Error message: You cannot delete this tax class as it is used for 1 customer groups.</p>
     *
     * @test
     */
    public function usedInCustomerGroup()
    {
        //Data
        $customerTaxClassData = $this->loadData('new_customer_tax_class', null, 'customer_class_name');
        $customerGroupData = $this->loadData('new_customer_group',
                                             array('tax_class' => $customerTaxClassData['customer_class_name']),
                                             'group_name');
        //Steps
        $this->taxHelper()->createCustomerTaxClass($customerTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->navigate('manage_customer_groups');
        $this->customerGroupsHelper()->createCustomerGroup($customerGroupData);
        $this->assertTrue($this->successMessage('success_saved_customer_group'), $this->messages);
        $this->navigate('manage_customer_tax_class');
        $this->taxHelper()->deleteTaxItem($customerTaxClassData ,'customer_tax_class');
        //Verifying
        $this->assertTrue($this->errorMessage('error_delete_tax_class_group'), $this->messages);
    }

    /**
     * Clean up
     */
    protected function tearDown()
    {
        //Remove Tax rule after test
        if (!is_null($this->ruleToBeDeleted)) {
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->ruleToBeDeleted ,'tax_rules');
            $this->ruleToBeDeleted = null;
        }
    }
}
