<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Attribute Validation Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExport_Customer_Attribute_ImportValidationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('import');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Customer Import, if file data is invalid
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5630
     */
    public function importCustomerFileWithInvalidData($customerData, array $validationMessage)
    {
        $this->navigate('manage_customer_attributes');
        $attrData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_yesno',
            array('values_required' => 'No'));
        $this->attributesHelper()->createAttribute($attrData);
        $customerData[$attrData['attribute_properties']['attribute_code']] = $this->generate('string', 5);
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Build CSV array
        $data = array($customerData);
        //Import file
        $importReport = $this->importExportHelper()->import($data);
        //Check import
        $validationMessage['validation']['error'] = str_replace(
            '%attribute_id%',
            $attrData['attribute_properties']['attribute_code'],
            $validationMessage['validation']['error']
        );
        $this->assertEquals($validationMessage, $importReport,
            'Import has been finished with issues ' . print_r($importReport, true));
    }

    public function importDataInvalid()
    {
        $customerDataRow = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
            'lastname' => 'last_' . $this->generate('string', 10),
            'firstname' => 'last_' . $this->generate('string', 10),
        ));
        return array(
            array($customerDataRow, array(
                'validation' => array(
                    'error' => array(
                        "Please correct the value for '%attribute_id%'. in rows: 1"
                    ),
                    'validation' => array(
                        "File is totally invalid. Please fix errors and re-upload file.",
                        "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1"
                    )
                )
            ))
        );
    }
}
