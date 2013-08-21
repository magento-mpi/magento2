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
 * Customer Finances Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExport_Customer_Finance_ImportValidationTest extends Mage_Selenium_TestCase
{
    protected static $_customerEmail = null;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        self::$_customerEmail = $userData['email'];
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Finances Import, if file data is invalid
     * Steps
     * Verify that import will not be started, if file has all rows that are invalid
     *
     * @test
     * @dataProvider importDataInvalid
     * @TestlinkId TL-MAGE-5643, TL-MAGE-5628
     */
    public function importFinanceFileWithInvalidData($financeData, array $validationMessage)
    {
        if (isset($financeData['_email']) && $financeData['_email'] == '<realEmail>') {
            $financeData['_email'] = self::$_customerEmail;
        }
        $this->navigate('import');
        //Step 1
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Build CSV array
        $data = array(
            $financeData
        );
        //Import file
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertEquals($validationMessage, $report, 'Import has been finished with issues');
    }

    public function importDataInvalid()
    {
        $userData[0] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array('_email' => ''));
        $errorMessage[0] = array("E-mail is not specified in rows: 1");

        $userData[1] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array('_email' => '<realEmail>'));
        unset($userData[1]['_website']);
        $errorMessage[1] = array("Cannot find required columns: _website");

        $userData[2] = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array('_email' => '<realEmail>', '_website' => 'notexist'));
        $errorMessage[2] = array("Invalid value in website column in rows: 1");

        $userData[3] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
            '_email' => '<realEmail>',
            'store_credit' => 'incorrect_value',
            'reward_points' => 'incorrect_value'
        ));
        $errorMessage[3] = array(
            "Please correct the value for 'store_credit'. in rows: 1",
            "Please correct the value for 'reward_points'. in rows: 1"
        );

        $userData[4] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array('_email' => '<realEmail>'));
        unset($userData[4]['credit_score']);
        unset($userData[4]['store_points']);

        $userData[5] = $this->loadDataSet('ImportExport', 'generic_finance_csv',
            array('_email' => 'test_admin_' . $this->generate('string', 5) . '@unknown-domain.com'));
        $errorMessage[5] = array("Customer with such email and website code doesn't exist in rows: 1");

        $invalidValidation[0] = array(
            "File is totally invalid. Please fix errors and re-upload file.",
            "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1"
        );

        $invalidValidation[1] = array(
            "File is totally invalid. Please fix errors and re-upload file.",
            "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 2"
        );

        $fixFileValidation = array(
            "Please fix errors and re-upload file."
        );

        return array(
            array($userData[0], array('validation' => array('error' => $errorMessage[0],
                'validation' => $invalidValidation[0]))),
            array($userData[1], array('validation' => array('error' => $errorMessage[1],
                'validation' => $fixFileValidation))),
            array($userData[2], array('validation' => array('error' => $errorMessage[2],
                'validation' => $invalidValidation[0]))),
            array($userData[3], array('validation' => array('error' => $errorMessage[3],
                'validation' => $invalidValidation[1]))),
            array($userData[4], array('validation' => array('validation' => array(
                "Checked rows: 1, checked entities: 1, invalid rows: 0, total errors: 0"),
                'success' => array("File is valid! To start import process press \"Import\" button  Import")),
                'import' => array('success' => array('Import successfully done')))),
            array($userData[5], array('validation' => array('error' => $errorMessage[5],
                'validation' => $invalidValidation[0])))
        );
    }

    /**
     * Import File with duplicated rows
     *
     * @test
     * @dataProvider fileDuplicatedRows
     *
     * @TestlinkId TL-MAGE-5951
     */
    public function importFileWithDuplicatedRows($csv, $errorMessage)
    {
        // set email and address id in csv file
        foreach ($csv as $key => $value) {
            $csv[$key]['_email'] = self::$_customerEmail;
        }

        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Add/Update Complex Data');
        //Steps 4-7
        $csvCustomAction = $csv;
        foreach ($csvCustomAction as $key => $value) {
            $csvCustomAction[$key]['_action'] = 'update';
        }

        $csv = array($csv, $csvCustomAction);

        foreach ($csv as $value) {
            $report = $this->importExportHelper()->import($value);

            //Verifying
            $this->assertArrayHasKey('import', $report, 'Import is not done');
            $this->assertArrayHasKey('error', $report['validation'],
                'Error notification is missing on the Check Data');
            $this->assertEquals($errorMessage, $report['validation']['error'][0],
                'Incorrect error message is displayed');
            $this->assertEquals(
                "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import",
                $report['validation']['validation'][0], 'Wrong validation message is shown');
            $this->assertEquals('Checked rows: 2, checked entities: 2, invalid rows: 1, total errors: 1',
                $report['validation']['validation'][1], 'Wrong message about checked rows');
        }
    }

    public function fileDuplicatedRows()
    {
        $csvMain = array(
            $this->loadDataSet('ImportExport', 'generic_finance_csv'),
            $this->loadDataSet('ImportExport', 'generic_finance_csv')
        );
        $errorMessageMain = 'Row with such email, website, finance website combination was already found. in rows: 2';

        return array(
            array($csvMain, $errorMessageMain),
        );
    }
}
