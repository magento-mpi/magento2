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
 * Rating creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community17_Mage_TermsAndConditions_CreateTest extends Mage_Selenium_TestCase {

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Checkout Terms and Conditions</p>
     */
    protected function assertPreConditions() {
        $this->loginAdminUser();
        $this->navigate('manage_checkout_terms_and_conditions');
    }

    /* Navigation to page
     * 
     * @ test
     */

    public function navigationNewTermsAndConditions() {
        $this->assertTrue($this->buttonIsPresent('create_new_terms_and_conditions'), 'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertTrue($this->checkCurrentPage('create_condition'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_condition'), 'There is no "Save Condition" button on the page');
    }

    /**
     * <p>Creating Terms and Conditions with required fields only</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Terms and condition saved</p>
     * @test
     * @TestlinkId    TL-MAGE-2312 
     */
    public function withRequiredFieldsOnly() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with required fields only</p>
     * <p>Status = ENABLED</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Terms and condition saved, status = ENABLED</p>
     * 
     * @test
     * 
     */
    public function withRequiredFieldsOnlyAndStatusEnabled() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default_status_enabled');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions</p>
     * <p>Show Content as = HTML</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Terms and condition status = ENABLED & Show Content as HTML</p>
     * 
     * @test
     * 
     */
    public function withRequiredFieldsOnlyAndShowContentAs() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default_show_content_as_html');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with several store views</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>Select several store view
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - Terms and condition saved, status = ENABLED</p>
     * 
     * @test
     * @TestlinkId    TL-MAGE-2246
     * 
     */
    public function withSeveralStoreViewsSelected() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_with_several_storeviews');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $termsData;
    }
        
    /** 
     *  
     * @test
     * 
     */
    public function withLongTermsAndConditionName() {
        //Data
        $longData = $this->loadDataSet('TermsAndConditions', 'generic_terms_long_values',
                array('condition_name'  => $this->generate('string', 255, ':alnum:'), 'checkbox_text' => $this->generate('string', 255, ':alnum:' ), 'content' => $this->generate('string', 255, ':alnum:')));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($longData);
        //Verification
        $this->assertMessagePresent('validation', 'condition_saved');
        return $longData;
    }
    
     /** Steps:
     * Create a T&C
     * Open just created
     * Retype "Condition Name" field -> Press "Save Condition" button
     * Result:
     * 
     * @test
     * 
     */
    public function withSpecialSymbolsTermsAndConditionName() {
        //Data
        $specialSymbols = $this->loadDataSet('TermsAndConditions', 'generic_terms_special_symbols',
                array('condition_name'  => $this->generate('string', 25, ':punct:'), 'checkbox_text' => $this->generate('string', 25, ':punct:' ), 'content' => $this->generate('string', 25, ':punct:')));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($specialSymbols);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');
        return $specialSymbols;
    }
      
    /**
     * <p>Creating Terms and Conditions with EMPTY condition name</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data; Don't fill Condition Name field</p>
     * <p>Select several store view </p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Impossible to Save Terms And Conditions</p>
     * 
     * @test
     * 
     */
    public function withConditionNameEmpty() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default', array('condition_name' => '%noValue%'));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('error', 'empty_required_condition_name_field');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with EMPTY Store View</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data; Don't fill Store View</p>
     * <p>Select several store view </p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Imposible to save T&C, store view is highlighted</p>
     * 
     * @test
     * 
     */
    public function withConditionStoreViewEmpty() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default', array('store_view' => '%noValue%'));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('error', 'empty_required_store_view');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with EMPTY CheckBox text</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data; Checkbox Text</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Imposible to save T&C, Checkbox Text is highlighted</p>
     * 
     * @test
     * 
     */
    public function withCheckBoxTextEmpty() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default', array('checkbox_text' => '%noValue%'));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('error', 'empty_required_checkbox_text');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with EMPTY Content text</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data; except Content Text</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Imposible to save T&C, Content Text is highlighted</p>
     * 
     * @test
     * 
     */
    public function withContentBoxEmpty() {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default', array('content' => '%noValue%'));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('error', 'empty_required_content');

        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with Content Height field  text</p>
     * <p>Steps:</p>
     * <p>1. Click "Add New Condition" button;</p>
     * <p>2. Fill in required fields by regular data; in Content Height enter more 25</p>
     * <p>3. Click "Save Condition" button;</p>
     * <p>Expected result:</p>
     * <p>Imposible to save T&C, Content Height is highlighted</p>
     * 
     * @test
     * 
     */
    public function withContentHeight() {
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_content_high_length'); //, array('content_height' => '%noValue%'));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verification
        $this->assertMessagePresent('error', 'content_height');

        return $termsData;
    }

}