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
 * Delete Terms And Conditions in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community17_Mage_TermsAndConditions_DeleteTest extends Mage_Selenium_TestCase {

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Checkout Terms and Conditions</p>
     */
    protected function assertPreConditions() {
        $this->loginAdminUser();
        $this->navigate('manage_checkout_terms_and_conditions');
    }

    /** Navigation to T&C page
     * 
     * @test
     */
    public function navigationNewTermsAndConditions() {
        $this->assertTrue($this->buttonIsPresent('create_new_terms_and_conditions'), 'There is no "Add New Condition" button on the page');
        $this->clickButton('create_new_terms_and_conditions');
        $this->assertTrue($this->checkCurrentPage('create_condition'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_condition'), 'There is no "Save Condition" button on the page');
    }

    /** Create a new Terms & Conditions for remove it
     * 
     * @test
     */
    public function preconditionsForTestTermsAndConditions() {
        //Data
        $simpleData = $this->loadDataSet('TermsAndConditions', 'generic_terms_default');
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'condition_saved');

        return $simpleData['condition_name'];
    }

    /**
     * @depends preconditionsForTestTermsAndConditions
     * 
     * @test
     * @TestLinkId	TL-MAGE-2319
     */
    public function deleteSingleTermsAndConditions($useData) {
        $searchData = $this->loadData('search_terms', array('filter_condition_name' => $useData));
        //Steps
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->searchAndOpen($searchData, true, 'sales_checkout_terms_and_conditions_grid');
        //Steps
        $this->termsAndConditionsHelper()->deleteTermsAndConditions($searchData);
        //Verification
        $this->assertMessagePresent('success', 'condition_deleted');
    }

}