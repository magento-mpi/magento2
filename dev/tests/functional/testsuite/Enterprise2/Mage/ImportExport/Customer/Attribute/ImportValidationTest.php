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
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }
    /**
     * <p>Customer Import, if file data is invalid</p>
     * <p>Steps</p>
     * <p>Verify that import will not be started, if file has all rows that are invalid</p>
     * <p>Invalid row is:</p>
     * <p>a row with empty value of required attribute</p>
     * <p>a row with wrong value of some system attribute (non existing website_id or group_id)</p>
     * <p>a row with invalid values for attributes that pass validation (wrong format of email)</p>
     * <p>value format differs from attribute input type (some text value is present for attribute with type Yes/No)</p>
     * <p>if the required column is absent in import file (email, website, firstname, group_id, lastname), file is invalid</p>
     * <p>Press "Check Data" button</p>
     * <p>Expected: Warning about incorrect file appears</p>
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
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customers Main File');
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
        $customerDataRow5 = $this->loadDataSet('ImportExport', 'generic_customer_csv',
            array(
                'email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10),
            ));
        return array(
            array($customerDataRow5,
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