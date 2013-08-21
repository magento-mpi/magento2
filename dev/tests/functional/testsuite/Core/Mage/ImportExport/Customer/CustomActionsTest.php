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
 * Customer Custom Actions Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class Core_Mage_ImportExport_Customer_CustomActionsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
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
     * Verify that import customers main file with specified action works correctly
     *
     * @test
     * @dataProvider importCustomAction
     * @TestlinkId TL-MAGE-5683, 5684, 5685
     */
    public function customImport($originalCustomerData, $customerRow, $updatedCustomerData, $validation)
    {
        //Precondition: create customer if needed for current test
        foreach ($originalCustomerData as $key => $value) {
            if (!is_null($originalCustomerData[$key])) {
                $this->navigate('manage_customers');
                $this->customerHelper()->createCustomer($originalCustomerData[$key]);
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
        }
        //Step 1
        $this->navigate('import');
        //Steps 2-3
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Custom Action');
        //Steps 4-6
        $report = $this->importExportHelper()->import($customerRow);
        //Verify import
        $this->assertEquals($validation, $report, 'Import has been finished with issues');
        //Steps 7-8: verifying customer data after import
        foreach ($updatedCustomerData as $key => $value) {
            $this->navigate('manage_customers');
            if (!is_null($value)) {
                $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($value),
                    'Customer not found');
                $this->customerHelper()->openCustomer(array('email' => $value['email']));
                $this->verifyForm($value, 'account_information', array('associate_to_website'));
                $this->assertEmptyVerificationErrors();
            } else {
                $this->assertFalse($this->customerHelper()->isCustomerPresentInGrid($originalCustomerData[$key]),
                    'Customer has not been deleted');
            }
        }
    }

    public function importCustomAction()
    {
        //custom behavior: update (3 test customers)
        $originalCustomerData[0] = array(array(), array(), array());
        $mainCsvRows[0] = array(array(), array(), array());
        $updatedCustomerData[0] = array(array(), array(), array());
        //custom behavior: delete (3 test customers)
        $originalCustomerData[1] = array(array(), array(), array());
        $mainCsvRows[1] = array(array(), array(), array());
        $updatedCustomerData[1] = array(array(), array(), array());
        //custom behavior: empty or not recognized (4 test customers)
        $originalCustomerData[2] = array(array(), array(), array(), array());
        $mainCsvRows[2] = array(array(), array(), array(), array());
        $updatedCustomerData[2] = array(array(), array(), array(), array());
        for ($i = 0; $i < 3; $i++) {
            foreach ($originalCustomerData[$i] as $key => &$value) {
                $value['associate_to_website'] = 'Main Website';
                $value['group'] = 'General';
                $value['email'] = 'test_admin_' . $this->generate('string', 5, ':digit:') . '@unknown-domain.com';
                $value['first_name'] = 'First_' . $this->generate('string', 5, ':digit:');
                $value['last_name'] = 'Last_' . $this->generate('string', 5, ':digit:');
                $updatedCustomerData[$i][$key] = $originalCustomerData[$i][$key];
                $value['password'] = '123123q';
                $mainCsvRows[$i][$key]['email'] = $originalCustomerData[$i][$key]['email'];
                $mainCsvRows[$i][$key]['_website'] = 'base';
                $mainCsvRows[$i][$key]['group_id'] = '1';
                $mainCsvRows[$i][$key]['firstname'] = $originalCustomerData[$i][$key]['first_name'];
                $mainCsvRows[$i][$key]['lastname'] = $originalCustomerData[$i][$key]['last_name'];
                $mainCsvRows[$i][$key]['reward_update_notification'] = 1;
                $mainCsvRows[$i][$key]['reward_warning_notification'] = 1;
            }
        }
        //update action. customer 1: different first name
        $mainCsvRows[0][0]['firstname'] = 'Update_' . $originalCustomerData[0][0]['first_name'];
        $updatedCustomerData[0][0]['first_name'] = $mainCsvRows[0][0]['firstname'];
        $mainCsvRows[0][0]['_action'] = 'update';
        //update action. customer 2: invalid email, different first name
        $mainCsvRows[0][1]['email'] = str_replace('@', '', $originalCustomerData[0][1]['email']);
        $mainCsvRows[0][1]['firstname'] = 'Update_' . $originalCustomerData[0][1]['first_name'];
        $mainCsvRows[0][1]['_action'] = 'Update';
        //update action. customer 3: new data
        unset($originalCustomerData[0][2]);
        $mainCsvRows[0][2]['_action'] = 'UPDATE';
        //delete action. customer 1: all attributes match
        unset($updatedCustomerData[1][0]);
        $mainCsvRows[1][0]['_action'] = 'delete';
        //delete action. customer 2: different first name, last name
        unset($updatedCustomerData[1][1]);
        $mainCsvRows[1][1]['firstname'] = 'Update_' . $originalCustomerData[1][1]['first_name'];
        $mainCsvRows[1][1]['lastname'] = 'Update_' . $originalCustomerData[1][1]['last_name'];
        $mainCsvRows[1][1]['_action'] = 'Delete';
        //delete action. customer 3: different website
        $mainCsvRows[1][2]['_website'] = 'admin';
        $mainCsvRows[1][2]['_action'] = 'DELETE';
        //empty or not recognized action. customer 1: different first name, last name
        $mainCsvRows[2][0]['firstname'] = 'Update_' . $originalCustomerData[2][0]['first_name'];
        $updatedCustomerData[2][0]['first_name'] = $mainCsvRows[2][0]['firstname'];
        $mainCsvRows[2][0]['lastname'] = 'Update_' . $originalCustomerData[2][0]['last_name'];
        $updatedCustomerData[2][0]['last_name'] = $mainCsvRows[2][0]['lastname'];
        $mainCsvRows[2][0]['_action'] = '';
        //empty or not recognized action. customer 2: empty first name, last name, group id
        $mainCsvRows[2][1]['firstname'] = '';
        $mainCsvRows[2][1]['lastname'] = '';
        $mainCsvRows[2][1]['group_id'] = '';
        $mainCsvRows[2][1]['_action'] = 'Please, delete';
        //empty or not recognized action. customer 3: new data
        unset($originalCustomerData[2][2]);
        $mainCsvRows[2][2]['_action'] = '';
        //empty or not recognized action. customer 4: new data
        unset($originalCustomerData[2][3]);
        unset($updatedCustomerData[2][3]);
        $mainCsvRows[2][3]['group_id'] = '1000';
        $mainCsvRows[2][3]['_action'] = 'Please, delete';
        //validation messages
        $fixErrorsMessage =
            "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import";
        $updateActionMessage = array('validation' => array(
            'error' => array("E-mail is invalid in rows: 2"),
            'validation' => array($fixErrorsMessage,
                "Checked rows: 3, checked entities: 3, invalid rows: 1, total errors: 1")),
            'import' => array('success' => array('Import successfully done')));
        $deleteActionMessage = array('validation' => array(
            'error' => array("Customer with such email and website code doesn't exist in rows: 3"),
            'validation' => array($fixErrorsMessage,
                "Checked rows: 3, checked entities: 3, invalid rows: 1, total errors: 1")),
            'import' => array('success' => array('Import successfully done')));
        $notRecognizedMessage = array('validation' => array(
            'error' => array("Please correct the value for 'group_id'. in rows: 4"),
            'validation' => array($fixErrorsMessage,
                "Checked rows: 4, checked entities: 4, invalid rows: 1, total errors: 1")),
            'import' => array('success' => array('Import successfully done')));
        return array(
            array($originalCustomerData[0], $mainCsvRows[0], $updatedCustomerData[0], $updateActionMessage),
            array($originalCustomerData[1], $mainCsvRows[1], $updatedCustomerData[1], $deleteActionMessage),
            array($originalCustomerData[2], $mainCsvRows[2], $updatedCustomerData[2], $notRecognizedMessage));
    }
}
