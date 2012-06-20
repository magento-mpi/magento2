<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iglazunova
 * Date: 6/18/12
 * Time: 3:51 PM
 * To change this template use File | Settings | File Templates.
 */
class Community2_Mage_ImportExport_AddressValidationTest extends Mage_Selenium_TestCase
{
    protected static $customerEmail = NULL;
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        if (is_null(self::$customerEmail)){
            $this->navigate('manage_customers');
            $userData = $this->loadDataSet('ImportExport', 'generic_customer_account');
            $this->customerHelper()->createCustomer($userData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            self::$customerEmail = $userData['email'];
        }
        //Step 1
        $this->navigate('import');

    }
    /**
     * <p>Address Import, if file data is invalid</p>
     * <p>Steps</p>
     * <p>Verify that import will not be started, if file has all rows that are invalid</p>
     * <p>Invalid row is:</p>
     * <p>a row with empty value of required attribute</p>
     * <p>a row with wrong value of some system attribute (non existing website_id or group_id)</p>
     * <p>a row with invalid values for attributes that pass validation (wrong format of email)</p>
     * <p>value format differs from attribute input type (some text value is present for attribute with type Yes/No)</p>
     * <p>if the required column is absent in import file (email, website, firstname, lastname, address_id, city, country_id, postcode, street, telephone), file is invalid</p>
     * <p>Press "Check Data" button</p>
     * <p>Expected: Warning about incorrect file appears</p>
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5631
     */
    public function importAddressFileWithInvalidData($addressData, array $validationMessage)
    {
        if (isset($addressData['_email']) && $addressData['_email']=='%realEmail%'){
            $addressData['_email'] = self::$customerEmail;
        }
        $this->admin('import');
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_behavior')
        );
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown','import_file_version')
        );
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible(
            $this->_getControlXpath('dropdown', 'import_customer_entity')
        );
        $this->fillDropdown('import_customer_entity', 'Customer Addresses');
        //Build CSV array
        $data = array(
            $addressData
        );
        //Import file
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertEquals($validationMessage, $report, 'Import has been finished with issues');
    }

    public function importDataInvalid()
    {
        $customerDataRow1 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_email' => '',
                'firstname' => '',
                'lastname' => ''
            ));
        $customerDataRow1['_entity_id'] = 'home';
        $customerDataRow2 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_email' => '%realEmail%',
                'lastname' => 'last_' . $this->generate('string',10)
            ));
        $customerDataRow2['_email'] = '%realEmail%';
        $customerDataRow2['_entity_id'] = 'home';
        unset($customerDataRow2['firstname']);
        $customerDataRow3 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_website' => 'notexist',
                '_email' => 'test_admin_' . $this->generate('string',5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10)
            ));
        $customerDataRow3['_entity_id'] = 'home';
        $customerDataRow4 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_email' => 'test_admin_' . $this->generate('string',5) . '@@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10)
            ));
        $customerDataRow4['_entity_id'] = 'home';
        $customerDataRow5 = $this->loadDataSet('ImportExport', 'import_address_file_required_fields1',
            array(
                '_email' => '%realEmail%',
                'lastname' => 'last_' . $this->generate('string',10),
                'firstname' => 'last_' . $this->generate('string',10),
            ));
        $customerDataRow5['_email'] = '%realEmail%';
        $customerDataRow5['_entity_id'] = 'home';
        $customerDataRow5['region'] = 'California1';
        return array(
            array($customerDataRow1, array('validation' => array(
                'error' => array(
                    "E-mail is not specified in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            ),
            array($customerDataRow2, array('validation' => array(
                'error' => array(
                    "Required attribute 'firstname' has an empty value in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            ),
            array($customerDataRow3, array('validation' => array(
                'error' => array(
                    "Invalid value in Website column (website does not exists?) in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            ),
            array($customerDataRow4, array('validation' => array(
                'error' => array(
                    "E-mail is invalid in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ),
            array($customerDataRow5, array('validation' => array(
                'error' => array(
                    "Region is invalid in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    )
                )
            )
        );
    }

}