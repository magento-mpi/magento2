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
 * Tax Rate creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxRate_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Manage Tax Zones&Rates</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_zones_and_rates');
    }

    /**
     * <p>Creating Tax Rate with required fields</p>
     *
     * @param string $taxRateDataSetName
     *
     * @return array $taxRateData
     * @test
     * @dataProvider withRequiredFieldsOnlyDataProvider
     * @TestlinkId TL-MAGE-3506
     */
    public function withRequiredFieldsOnly($taxRateDataSetName)
    {
        //Data
        $rate = $this->loadDataSet('Tax', $taxRateDataSetName);
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $rate['tax_identifier']));
        //Steps
        $this->taxHelper()->createTaxRate($rate);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->taxHelper()->openTaxItem($search, 'rate');
        //Verifying
        $this->assertTrue($this->verifyForm($rate), $this->getParsedMessages());
    }

    public function withRequiredFieldsOnlyDataProvider()
    {
        return array(
            array('tax_rate_create_test_zip_no'), // Zip/Post is Range => No
            array('tax_rate_create_test_zip_yes') // Zip/Post is Range => Yes
        );
    }

    /**
     * <p>Creating Tax Rate with name that exists</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withTaxIdentifierThatAlreadyExists()
    {
        //Steps
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        //Steps
        $this->taxHelper()->createTaxRate($taxRateData);
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        $this->taxHelper()->createTaxRate($taxRateData);
        //Verifying
        $this->assertMessagePresent('error', 'code_already_exists');
    }

    /**
     * <p>Creating a Tax Rate with empty required fields.</p>
     *
     * @param string $emptyFieldName Name of the field to leave empty
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @TestlinkId TL-MAGE-3506
     */
    public function withEmptyRequiredFields($emptyFieldName)
    {
        //Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_yes', array($emptyFieldName => ''));
        //Steps
        $this->taxHelper()->createTaxRate($taxRateData);
        //Verifying
        $this->addFieldIdToMessage('field', $emptyFieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('tax_identifier'),
            array('rate_percent'),
            array('zip_range_from'),
            array('zip_range_to')
        );
    }

    /**
     * Fails because of MAGE-5237
     *
     * @param array $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     * @TestlinkId TL-MAGE-3509
     */
    public function withSpecialValues($specialValue)
    {
        //Data
        $taxRate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no', array('tax_identifier' => $specialValue));
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $taxRate['tax_identifier']));
        //Steps
        $this->taxHelper()->createTaxRate($taxRate);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->taxHelper()->openTaxItem($search, 'rate');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRate), $this->getParsedMessages());
    }

    public function withSpecialValuesDataProvider()
    {
        return array(
            array($this->generate('string', 255)),
            array($this->generate('string', 50, ':punct:'))
        );
    }

    /**
     * <p>Creating a new Tax Rate with invalid values for Range From\To.</p>
     *
     * @param array $specialValue
     *
     * @test
     * @dataProvider withInvalidValuesForRangeDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3508
     */
    public function withInvalidValuesForRange($specialValue)
    {
        //Data
        $taxRateData =
            $this->loadDataSet('Tax', 'tax_rate_create_test_zip_yes', array('zip_range_from' => $specialValue,
                                                                            'zip_range_to'   => $specialValue));
        //Steps
        $this->taxHelper()->createTaxRate($taxRateData);
        //Verifying
        $this->addFieldIdToMessage('field', 'zip_range_from');
        $this->assertMessagePresent('error', 'enter_valid_digits');
        $this->addFieldIdToMessage('field', 'zip_range_from');
        $this->assertMessagePresent('error', 'enter_valid_digits');
    }

    public function withInvalidValuesForRangeDataProvider()
    {
        return array(
            array($this->generate('string', 50)), //string
            array($this->generate('string', 2, ':digit:') . " " . $this->generate('string', 2, ':digit:')), //with space
            array($this->generate('string', 50, ':punct:')) //special chars
        );
    }

    /**
     * <p>Creating a new Tax Rate with invalid values for Rate Percent.</p>
     *
     * @param array $specialValue
     *
     * @test
     * @dataProvider withInvalidValueForRatePercentDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3507
     */
    public function withInvalidValueForRatePercent($specialValue)
    {
        //Data
        $taxRateData =
            $this->loadDataSet('Tax', 'tax_rate_create_test_zip_yes', array('rate_percent' => $specialValue));
        //Steps
        $this->taxHelper()->createTaxRate($taxRateData);
        //Verifying
        $this->addFieldIdToMessage('field', 'rate_percent');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
    }

    public function withInvalidValueForRatePercentDataProvider()
    {
        return array(
            array($this->generate('string', 50, ':alpha:')),
            array($this->generate('string', 50, ':punct:'))
        );
    }

    /**
     * <p>Creating a new Tax Rate with State.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3505
     */
    public function withSelectedState()
    {
        //Data
        $taxRate = $this->loadDataSet('Tax', 'tax_rate_create_with_custom_state');
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $taxRate['tax_identifier']));
        //Steps
        $this->taxHelper()->createTaxRate($taxRate);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Steps
        $this->taxHelper()->openTaxItem($search, 'rate');
        //Verifying
        $this->assertTrue($this->verifyForm($taxRate), $this->getParsedMessages());
    }

    /**
     * <p>Creating a new Tax Rate with custom store view titles.</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     */
    public function withStoreViewTitle()
    {
        $this->markTestIncomplete('MAGETWO-9043');
        //Preconditions
        $this->navigate('manage_stores');
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Data
        $storeViewName = $storeViewData['store_view_name'];
        $taxRate = $this->loadDataSet('Tax', 'tax_rate_create_with_store_views');
        $taxRate['tax_titles'][$storeViewName] = 'tax rate title for ' . $storeViewName;
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $taxRate['tax_identifier']));
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRate);
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        $this->taxHelper()->openTaxItem($search, 'rate');
        //Verification
        $this->assertTrue($this->verifyForm($taxRate), $this->getParsedMessages());
    }
}