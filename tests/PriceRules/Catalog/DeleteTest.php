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
 * Catalog Price Rule Delete
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_Catalog_DeleteTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_catalog_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_catalog_price_rules'), $this->messages);
    }

    /**
     * <p>Create catalog price rule - required fields</p>
     *
     * <p>Steps</p>
     * <p>1. Click "Add New Rule"</p>
     * <p>2. Fill in required fields in all tab</p>
     * <p>3. Click "Save Rule" button</p>
     *
     * <p>Expected result: New rule created, success message appears</p>
     *
     * @test
     */
    public function createCatalogPriceRule()
    {
        //Data
        $priceRuleData = $this->loadData('test_catalog_rule', array('customer_groups' => 'General'), 'rule_name');
        //Steps
        $this->priceRulesHelper()->createRule($priceRuleData);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->successMessage('notification_message'), $this->messages);
        $this->verifyMessagesCount(2);
        $this->search(array('filter_rule_name' => $priceRuleData['info']['rule_name']));
        return $priceRuleData['info']['rule_name'];
    }

    /**
     * <p>Delete created rule</p>
     *
     * <p>1. Open created Rule</p>
     * <p>2. Click "Delete Rule" button</p>
     * <p>3. Click "Ok" in confirmation window</p>
     * <p>4. Check confirmation message</p>
     *
     * <p>Expected result: Success message appears, rule removed from the list</p>
     *
     * @depends createCatalogPriceRule
     * @test
     */
    public function catalogPriceRuleDelete($createdRuleName)
    {
        //Steps
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->addParameter('elementTitle', $createdRuleName);
        $this->searchAndOpen(array($createdRuleName));
        $this->clickButtonAndConfirm('delete_rule', 'confirmation_for_delete');
        $this->assertTrue($this->successMessage('success_deleted_rule', $this->messages));
        //Verification
        $this->assertEquals(NULL, $this->search(array('rule_name' => $createdRuleName)));
    }
}
