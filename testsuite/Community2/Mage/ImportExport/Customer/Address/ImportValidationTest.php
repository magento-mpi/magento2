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
 * Customer Addresses Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ImportExport_ImportValidation_AddressTest extends Mage_Selenium_TestCase
{
    protected static $_customerEmail = NULL;

    /**
     * Precondition:
     * Create a customer
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        self::$_customerEmail = $userData['email'];
    }

    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');

    }
    /**
     * Address Import, if file data is invalid
     * Steps
     * Verify that import will not be started, if file has all rows that are invalid
     * Invalid row is:
     * a row with empty value of required attribute
     * a row with wrong value of some system attribute (non existing website_id or group_id)
     * a row with invalid values for attributes that pass validation (wrong format of email)
     * value format differs from attribute input type (some text value is present for attribute with type Yes/No)
     * if the required column is absent in import file (email, website, firstname, lastname, address_id, city,
     * country_id, postcode, street, telephone), file is invalid
     * Press "Check Data" button
     * Expected: Warning about incorrect file appears
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5631, TL-MAGE-5626
     */
    public function importAddressFileWithInvalidData($addressCsv, $validationMessage)
    {
        if (isset($addressCsv['_email']) && $addressCsv['_email']=='<realEmail>') {
            $addressCsv['_email'] = self::$_customerEmail;
        }
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Add/Update Complex Data');
        //Build CSV array
        $data = array(
            $addressCsv
        );
        //Import file
        $report = $this->importExportHelper()->import($data) ;
        //Check import
        $this->assertEquals($validationMessage, $report, 'Import has been finished with issues');
    }

    public function importDataInvalid()
    {
        $addressCsv[0] = $this->loadDataSet('ImportExport', 'generic_address_csv', array(
            '_email' => '',
            'firstname' => '',
            'lastname' => '',
            '_entity_id' => 'home'
            ));
        $addressCsv[1] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array(
                '_email' => '<realEmail>',
                'firstname' => '%noValue%',
                'lastname' => 'last_' . $this->generate('string', 10),
                '_entity_id' => 'home'));
        $addressCsv[2] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array(
                '_website' => 'notexist',
                '_email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
                '_entity_id' => 'home'));
        $addressCsv[3] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array(
                '_email' => 'test_admin_' . $this->generate('string', 5) . '@@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
                '_entity_id' => 'home'));
        $addressCsv[4] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array(
                '_email' => '<realEmail>',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
                '_entity_id' => 'home',
                'region' => 'California1'));
        $addressCsv[5] = $this->loadDataSet('ImportExport', 'generic_address_csv',
            array(
                '_email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com',
                'lastname' => 'last_' . $this->generate('string', 10),
                'firstname' => 'last_' . $this->generate('string', 10),
                '_entity_id' => 'home'));
        return array(
            array($addressCsv[0], array('validation' => array(
                'error' => array(
                    "E-mail is not specified in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    ))),
            array($addressCsv[1], array('validation' => array(
                'error' => array(
                    "Required attribute 'firstname' has an empty value in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    ))),
            array($addressCsv[2], array('validation' => array(
                'error' => array(
                    "Invalid value in website column in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    ))),
            array($addressCsv[3], array('validation' => array(
                'error' => array(
                    "E-mail is invalid in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            ))),
            array($addressCsv[4], array('validation' => array(
                'error' => array(
                    "Region is invalid in rows: 1"
                    ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
                    ))),
            array($addressCsv[5], array('validation' => array(
                'error' => array(
                    "Customer with such email and website code doesn't exist in rows: 1"
                ),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1")
            )
            )
            ));
    }
}