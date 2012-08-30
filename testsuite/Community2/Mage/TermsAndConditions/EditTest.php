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
 * Rating creation into backend
 *
 * @package     selenium
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_TermsAndConditions_EditTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Terms and Conditions</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_checkout_terms_and_conditions');
    }

    /**
     * <p>Steps:</p>
     * <p>1. Open just created Terms and Conditions</p>
     * <p>2. Navigate "Condition Name", "Checkbox Text", "Content" field.</p>
     * <p>3. Retype information in the fields.</p>
     * <p>4. Press "Save Condition" button.</p>
     * <p>Expected Result:</p>
     * <p>T&C is saved, changes are applied.</p>
     *
     * @test
     * @TestLinkId TL-MAGE-2314
     */
    public function editTermsAndConditions()
    {
        //Data
        $terms = $this->loadDataSet('TermsAndConditions', 'generic_terms_required');
        $searchTerms = $this->loadDataSet('TermsAndConditions', 'search_terms_and_conditions',
            array('filter_condition_name' => $terms['condition_name']));
        $editTermsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_all');
        $searchTermsEdited = $this->loadDataSet('TermsAndConditions', 'search_terms_and_conditions',
            array('filter_condition_name' => $editTermsData['condition_name']));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($terms);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        //Steps
        $this->termsAndConditionsHelper()->openTermsAndConditions($searchTerms);
        $this->fillFieldset($editTermsData, 'sales_new_condition');
        $this->saveForm('save_condition');
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        //Steps
        $this->termsAndConditionsHelper()->openTermsAndConditions($searchTermsEdited);
        //Verifying
        $this->termsAndConditionsHelper()->verifyTermsAndConditions($editTermsData);
    }
}