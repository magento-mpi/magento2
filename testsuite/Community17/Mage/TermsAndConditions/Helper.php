<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_TermsAndConditions
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community17_Mage_TermsAndConditions_Helper extends Mage_Selenium_TestCase {
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
