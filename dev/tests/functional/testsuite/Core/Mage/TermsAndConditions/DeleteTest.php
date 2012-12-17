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
 * Delete Terms And Conditions in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_TermsAndConditions_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Manage Terms and Conditions</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_sales_checkout_terms_conditions');
    }

    /**
     * <p>Delete created Terms & Conditions</p>
     *
     * @test
     * @TestLinkId TL-MAGE-2319
     */
    public function deleteSingleTermsAndConditions()
    {
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_required');
        $searchTerms = $this->loadDataSet('TermsAndConditions', 'search_terms_and_conditions',
            array('filter_condition_name' => $termsData['condition_name']));
        //Steps
        $this->navigate('manage_sales_checkout_terms_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        //Steps
        $this->termsAndConditionsHelper()->deleteTerms($searchTerms);
        //Verifying
        $this->assertMessagePresent('success', 'condition_deleted');
    }
}