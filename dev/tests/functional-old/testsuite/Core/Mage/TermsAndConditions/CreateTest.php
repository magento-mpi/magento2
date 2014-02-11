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
 * Create Terms and Conditions in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_TermsAndConditions_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Terms and Conditions grid</p>
     * @test
     */
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_sales_checkout_terms_conditions');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('manage_sales_checkout_terms_conditions');
        $this->termsAndConditionsHelper()->deleteAllTerms();
    }

    /**
     * <p>Create Terms and Conditions with mandatory fields only</p>
     *
     * @return array $termsData
     *
     * @test
     * @TestLinkId TL-MAGE-2241
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_required');
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        return $termsData;
    }

    /**
     * <p>Creating Terms and Conditions with name that already exists</p>
     *
     * @param $termsData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestLinkId
     */
    public function withAlreadyExistingTerms($termsData)
    {
        $this->markTestIncomplete('MAGETWO-1708');
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('error');
    }

    /**
     * <p>Creating Terms and Conditions (Show Content As = HTML)</p>
     *
     * @test
     * @TestLinkId TL-MAGE-2255
     */
    public function withRequiredFieldsOnlyAndShowContentAs()
    {
        //Data
        $termsData =
            $this->loadDataSet('TermsAndConditions', 'generic_terms_required', array('show_content_as' => 'HTML'));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
    }

    /**
     * <p>Create New Terms and Conditions (Empty mandatory fields)</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-2313
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $termsData =
            $this->loadDataSet('TermsAndConditions', 'generic_terms_required', array($emptyField => '%noValue%'));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->addParameter('fieldId', $this->_getControlXpath($fieldType, $emptyField));
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('condition_name', 'field'),
            array('store_view', 'multiselect'),
            array('checkbox_text', 'field'),
            array('content', 'field')
        );
    }

    /**
     * <p>Creating Terms and Conditions (Several Store Views)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2246
     */
    public function withSeveralStoreViewsSelected()
    {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_required',
            array('store_view' => 'All Store Views, Default Store View'));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
    }

    /**
     * <p>Creating Terms and Conditions(Content Height with wrong value)</p>
     *
     * @test
     * @TestLinkId TL-MAGE-2266
     */
    public function withContentHeight()
    {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_required',
            array('content_height' => $this->generate('string', 25, ':punct:')));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('validation', 'wrong_content_height');
    }

    /**
     * <p>Create Terms and Conditions (all required fields are filled by long value data = 255).</p>
     *
     * @test
     * @TestLinkId TL-MAGE-5627
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_all', array(
            'condition_name' => $this->generate('string', 255, ':alnum:'),
            'checkbox_text'  => $this->generate('string', 255, ':alnum:'),
            'content'        => $this->generate('string', 255, ':alnum:')
        ));
        $termsToOpen = $this->loadDataSet('TermsAndConditions', 'search_terms_and_conditions',
            array('filter_condition_name' => $termsData['condition_name']));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        //Steps
        $this->termsAndConditionsHelper()->openTermsAndConditions($termsToOpen);
        //Verifying
        $this->termsAndConditionsHelper()->verifyTermsAndConditions($termsToOpen);
    }

    /**
     * <p>Create Terms and Conditions (with special characters).</p>
     *
     * @test
     * @TestLinkId TL-MAGE-5629
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_all',array(
            'condition_name' => $this->generate('string', 32, ':punct:'),
            'checkbox_text'  => $this->generate('string', 32, ':punct:'),
            'content'        => $this->generate('string', 32, ':punct:')
        ));
        $termsToOpen = $this->loadDataSet('TermsAndConditions', 'search_terms_and_conditions',
            array('filter_condition_name' => $termsData['condition_name']));
        //Steps
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        //Verifying
        $this->assertMessagePresent('success', 'condition_saved');
        //Steps
        $this->termsAndConditionsHelper()->openTermsAndConditions($termsToOpen);
        //Verifying
        $this->termsAndConditionsHelper()->verifyTermsAndConditions($termsToOpen);
    }
}
