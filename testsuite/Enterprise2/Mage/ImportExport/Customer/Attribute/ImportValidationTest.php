<?php
/**
 * Magento
 *
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

class Enterprise2_Mage_ImportExport_AttributeValidation_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }
    /**
     * Customer Import, if file data is invalid
     * Steps
     * Verify that import will not be started, if file has all rows that are invalid
     * Invalid row is:
     * a row with empty value of required attribute
     * a row with wrong value of some system attribute (non existing website_id or group_id)
     * a row with invalid values for attributes that pass validation (wrong format of email)
     * value format differs from attribute input type (some text value is present for attribute with type Yes/No)
     * if the required column is absent in import file (email, website, firstname, group_id, lastname), file is invalid
     * Press "Check Data" button
     * Expected: Warning about incorrect file appears
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
        $customerData[$attrData['properties']['attribute_code']] = 'gf13gh';
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        //Build CSV array
        $data = array(
            $customerData
        );
        //Import file
        $importReport = $this->importExportHelper()->import($data) ;
        //Check import
        $validationMessage['validation']['error'] = str_replace(
            '%attribute_id%',
            $attrData['properties']['attribute_code'],
            $validationMessage['validation']['error']
        );
        $this->assertEquals($validationMessage, $importReport,
            'Import has been finished with issues ' . print_r($importReport, true));
    }

    public function importDataInvalid()
    {
        $customerDataRow = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
            ));
        return array(
            array($customerDataRow,
                array(
                    'validation' => array(
                        'error' => array(
                            "Invalid value for '%attribute_id%' in rows: 1"
                        ),
                    'validation' => array(
                        "File is totally invalid. Please fix errors and re-upload file",
                        "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1"
                        )
                    )
                )
            )
        );
    }
}