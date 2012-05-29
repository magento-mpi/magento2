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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_TermsAndConditions_Helper extends Mage_Selenium_TestCase {
    /* Create a simple Terms and Conditions
     * 
     */

    public function createTermsAndConditions($termsData) {
        $termsData = $this->arrayEmptyClear($termsData);
        $this->clickButton('create_new_terms_and_conditions');
        $this->fillForm($termsData);
        $titleName = (isset($termsData['condition_name'])) ? $termsData['condition_name'] : '';
        $this->addParameter('title_condition', $titleName);
        $this->saveForm('save_condition');
    }

    /* Opens terms and conditions
     * 
     * @param array $termsSearch
     */

    public function openTermsAndConditions(array $termsSearch) {

        $termsSearch = $this->arrayEmptyClear($termsSearch);
        $xpathTR = $this->search($termsSearch, 'sales_checkout_terms_and_conditions_grid');
        $this->assertNotEquals(null, $xpathTR, 'Terms is not found');
        $param = $this->getText($xpathTR . '/td[' . $this->getColumnIdByName('Condition Name') . ']');
        $this->addParameter('title_condition', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->click($xpathTR);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
    }

    /**
     * Edit existing rating
     *
     * @param $termsData
     */
    public function editTermsAndCondtions($termsData) {

        //$this->openTermsAndConditions($termsData);
        $this->fillForm('generic_terms_default');
        $this->saveForm('save_condition');
    }

    /**
     * Open Terms And Conditions -> delete
     *
     * @param array $searchData
     */
    public function deleteTermsAndConditions(array $searchData) {
        $this->clickButtonAndConfirm('delete_condition', 'confirmation_for_delete');
    }

}