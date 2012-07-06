<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ibabenko
 * Date: 02.07.12
 * Time: 18:38
 * To change this template use File | Settings | File Templates.
 */
class Community2_Mage_ImportExport_CustomActions_CustomerTest extends Mage_Selenium_TestCase
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
     * <p>Verify that import customers main file with specified action works correctly</p>
     * <p>Precondition: 3 csv files, several rows each (update, delete, empty or not recognized actions)</p>
     * <p>Customers exist in the system</p>
     * <p>Steps:</p>
     * <p>1. Go to System -> Import/Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Import Format Version: Magento 2.0 format</p>
     * <p>4. Select Import Behavior: Custom Action</p>
     * <p>5. Select Customer Entity Type: Customers Main File</p>
     * <p>6. Select file from precondition</p>
     * <p>7. Click Check Data button</p>
     * <p>8. Click Import button</p>
     * <p>9. Go to Customers -> Manage Customers</p>
     * <p>10. Open customer (if not deleted after import)</p>
     * <p>After step 7: corresponding validation messages are shown</p>
     * <p>After step 10: required action applied to customer data</p>
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
                $this->admin('manage_customers');
                $this->customerHelper()->createCustomer($originalCustomerData[$key]);
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
        }
        //Step 1
        $this->admin('import');
        //Steps 2-5
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customers Main File');
        //Steps 6-8
        $report = $this->importExportHelper()->import($customerRow);
        //Verify import
        $this->assertEquals($validation, $report, 'Import has been finished with issues');
        //Steps 9-10: verifying customer data after import
        foreach ($updatedCustomerData as $key => $value) {
            $this->admin('manage_customers');
            if (!is_null($updatedCustomerData[$key])) {
                $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($updatedCustomerData[$key]),
                    'Customer not found');
                $this->addParameter('customer_first_last_name', $updatedCustomerData[$key]['first_name']
                    . ' ' . $updatedCustomerData[$key]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => $updatedCustomerData[$key]['email']));
                $this->assertTrue($this->verifyForm($updatedCustomerData[$key], 'account_information'),
                    'Customer has not been updated');
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

        foreach ($originalCustomerData as $key => $value) {
            foreach ($originalCustomerData[$key] as $key1 => $value1) {
                $originalCustomerData[$key][$key1]['associate_to_website'] = 'Main Website';
                $originalCustomerData[$key][$key1]['group'] = 'General';
                $originalCustomerData[$key][$key1]['email'] =
                    'test_admin_' . $this->generate('string', 5, ':digit:') . '@unknown-domain.com';
                $originalCustomerData[$key][$key1]['first_name'] =
                    'First_' . $this->generate('string', 5, ':digit:');
                $originalCustomerData[$key][$key1]['last_name'] =
                    'Last_' . $this->generate('string', 5, ':digit:');
                $updatedCustomerData[$key][$key1] = $originalCustomerData[$key][$key1];
                $originalCustomerData[$key][$key1]['password'] = '123123q';
                $mainCsvRows[$key][$key1]['email'] = $originalCustomerData[$key][$key1]['email'];
                $mainCsvRows[$key][$key1]['_website'] = 'base';
                $mainCsvRows[$key][$key1]['group_id'] = '1';
                $mainCsvRows[$key][$key1]['firstname'] = $originalCustomerData[$key][$key1]['first_name'];
                $mainCsvRows[$key][$key1]['lastname'] = $originalCustomerData[$key][$key1]['last_name'];
				$mainCsvRows[$key][$key1]['reward_update_notification'] = 1;
				$mainCsvRows[$key][$key1]['reward_warning_notification'] = 1;
            }
        }

        //update action
        //customer 1: different first name
        $mainCsvRows[0][0]['firstname'] = 'Update_' . $originalCustomerData[0][0]['first_name'];
        $updatedCustomerData[0][0]['first_name'] = $mainCsvRows[0][0]['firstname'];
        $mainCsvRows[0][0]['_action'] = 'update';
        //customer 2: invalid email, different first name
        $mainCsvRows[0][1]['email'] = str_replace('@', '', $originalCustomerData[0][1]['email']);
        $mainCsvRows[0][1]['firstname'] = 'Update_' . $originalCustomerData[0][1]['first_name'];
        $mainCsvRows[0][1]['_action'] = 'Update';
        //customer 3: new data
        unset($originalCustomerData[0][2]);
        $mainCsvRows[0][2]['_action'] = 'UPDATE';

        //delete action
        //customer 1: all attributes match
        unset($updatedCustomerData[1][0]);
        $mainCsvRows[1][0]['_action'] = 'delete';
        //customer 2: different first name, last name
        unset($updatedCustomerData[1][1]);
        $mainCsvRows[1][1]['firstname'] = 'Update_' . $originalCustomerData[1][1]['first_name'];
        $mainCsvRows[1][1]['lastname'] = 'Update_' . $originalCustomerData[1][1]['last_name'];
        $mainCsvRows[1][1]['_action'] = 'Delete';
        //customer 3: different website
        $mainCsvRows[1][2]['_website'] = 'admin';
        $mainCsvRows[1][2]['_action'] = 'DELETE';

        //empty or not recognized action
        //customer 1: different first name, last name
        $mainCsvRows[2][0]['firstname'] = 'Update_' . $originalCustomerData[2][0]['first_name'];
        $updatedCustomerData[2][0]['first_name'] = $mainCsvRows[2][0]['firstname'];
        $mainCsvRows[2][0]['lastname'] = 'Update_' . $originalCustomerData[2][0]['last_name'];
        $updatedCustomerData[2][0]['last_name'] = $mainCsvRows[2][0]['lastname'];
        $mainCsvRows[2][0]['_action'] = '';
        //customer 2: empty first name, last name, group id
        $mainCsvRows[2][1]['firstname'] = '';
        $mainCsvRows[2][1]['lastname'] = '';
        $mainCsvRows[2][1]['group_id'] = '';
        $mainCsvRows[2][1]['_action'] = 'Please, delete';
        //customer 3: new data
        unset($originalCustomerData[2][2]);
        $mainCsvRows[2][2]['_action'] = '';
        //customer 4: new data
        unset($originalCustomerData[2][3]);
        unset($updatedCustomerData[2][3]);
        $mainCsvRows[2][3]['group_id'] = '1000';
        $mainCsvRows[2][3]['_action'] = 'Please, delete';

        //validation messages
        $t = "Please fix errors and re-upload file or simply press \"Import\" button to skip rows with errors  Import";
        $updateActionMessage = array('validation' => array(
            'error' => array("E-mail is invalid in rows: 2"),
            'validation' => array($t, "Checked rows: 3, checked entities: 3, invalid rows: 1, total errors: 1")
        ),
            'import' => array('success' => array('Import successfully done.'))
        );
        $deleteActionMessage = array('validation' => array(
            'error' => array("Customer with such email and website code doesn't exist in rows: 3"),
            'validation' => array($t, "Checked rows: 3, checked entities: 3, invalid rows: 1, total errors: 1")
        ),
            'import' => array('success' => array('Import successfully done.'))
        );
        $notRecognizedActionMessage = array('validation' => array(
            'error' => array("Invalid value for 'group_id' in rows: 4"),
            'validation' => array($t, "Checked rows: 4, checked entities: 4, invalid rows: 1, total errors: 1")
        ),
            'import' => array('success' => array('Import successfully done.'))
        );

        return array(
            array($originalCustomerData[0], $mainCsvRows[0], $updatedCustomerData[0], $updateActionMessage),
            array($originalCustomerData[1], $mainCsvRows[1], $updatedCustomerData[1], $deleteActionMessage),
            array($originalCustomerData[2], $mainCsvRows[2], $updatedCustomerData[2], $notRecognizedActionMessage),
        );
    }
}