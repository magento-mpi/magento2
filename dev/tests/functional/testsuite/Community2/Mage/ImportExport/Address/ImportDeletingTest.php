<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ibabenko
 * Date: 29.06.12
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */
class Community2_Mage_ImportExport_AddressDelete extends Mage_Selenium_TestCase
{
    protected static $customerData = array();

    /**
     * <p>Precondition:</p>
     * <p>Create new customer</p>
     */
    public function setUpBeforeTests()
    {
       $this->loginAdminUser();
       $this->admin('manage_customers');
       self::$customerData = $this->loadDataSet('ImportExport.yml', 'generic_customer_account');
       $this->customerHelper()->createCustomer(self::$customerData);
       $this->assertMessagePresent('success', 'success_saved_customer');
    }
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
    }
    /**
     * <p>Verify that deleting customer address via import works correctly</p>
     * <p>Preconditions: One customer with three addresses created in the system</p>
     * <p>5 csv files: 1 - full match to customer address data (positive);</p>
     * <p>2 - only unique key match (positive); 3 - different email (negative);</p>
     * <p>4 - different website (negative); 5 - different address id (negative)</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Import Behavior: Delete Entities</p>
     * <p>4. Select Import Format Version: Magento 2.0 format</p>
     * <p>5. Select Customer Entity Type: Customer Addresses</p>
     * <p>6. Select file from precondition</p>
     * <p>7. Click Check Data button</p>
     * <p>8. Click Import button</p>
     * <p>9. Go to Customers -> Manage Customers</p>
     * <p>10. Open customer, check addresses</p>
     * <p>Expected:</p>
     * <p>After step 7: corresponding validation messages are shown</p>
     * <p>After step 10: no address in positive cases; address present in negative cases</p>
     *
     * @test
     * @dataProvider importDeleteAddress
     * @TestlinkId TL-MAGE-5679, 5680
     */
    public function deleteCustomerAddress($addressData, $addressRow, $shouldBeDeleted, $validation)
    {
        //Add address for customer if not present
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', self::$customerData['first_name']
            . ' ' . self::$customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => self::$customerData['email']));
        $this->openTab('addresses');
        if ($this->customerHelper()->isAddressPresent($addressData) == 0) {
            $this->customerHelper()->addAddress($addressData);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => self::$customerData['email']));
            $this->openTab('addresses');
        };
        $addressId = $this->customerHelper()->isAddressPresent($addressData);

        //Step 1
        $this->admin('import');
        //Steps 2-5
        $this->importExportHelper()->chooseImportOptions('Customers', 'Delete Entities',
            'Magento 2.0 format', 'Customer Addresses');
        //Steps 6-8
        if($addressRow[0]['_email'] == '%realEmail%') {
            $addressRow[0]['_email'] = self::$customerData['email'];
        }
        if($addressRow[0]['_entity_id'] == '%realAddressId%') {
            $addressRow[0]['_entity_id'] = $addressId;
        }
        $report = $this->importExportHelper()->import($addressRow);
        //Verify import
        $this->assertEquals($validation, $report, 'Import has been finished with issues');
        //Steps 9-10
        $this->admin('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => self::$customerData['email']));
        $this->openTab('addresses');
        //Verifying that address was deleted/not deleted by import
        if ($shouldBeDeleted) {
            $this->assertEquals(0, $this->customerHelper()->isAddressPresent($addressData),
                'Address wasn\'t deleted by import');
        } else {
            $this->assertNotEquals(0, $this->customerHelper()->isAddressPresent($addressData),
                'Address was deleted');
        }
    }

    public function importDeleteAddress()
    {
        $basicAddressData = $this->loadDataSet('ImportExport', 'data_valid_address1_5636');
        $basicAddressRow = $this->loadDataSet('ImportExport', 'csv_valid_address1_5636');
        $addressData = array();
        $addressRows = array();
        $streets = array('4040 Hickory Ridge Drive', '3129 Parkway Street', '746 Goodwin Avenue');
        for ($i=0; $i<6; $i++) {
            $addressData[$i] = $basicAddressData;
            $addressRows[$i] = $basicAddressRow;
            $addressRows[$i]['_email'] = '%realEmail%';
            $addressRows[$i]['_entity_id'] = '%realAddressId%';
            if ($i<3) {
                $addressData[$i]['street_address_line_1'] = $streets[$i];
                $addressRows[$i]['street'] = $streets[$i];
            } else {
                $addressData[$i]['street_address_line_1'] = $streets[2];
                $addressRows[$i]['street'] = $streets[2];
            }
        }
        //row 1 matches customer address data
        //row 2: only unique key match
        $addressRows[1]['city'] = 'Volga';
        $addressRows[1]['firstname'] = 'Nicole';
        $addressRows[1]['lastname'] = 'Forrest';
        $addressRows[1]['postcode'] = '57071';
        $addressRows[1]['street'] = '1181 Ryan Road';
        $addressRows[1]['telephone'] = '605-627-7815';
        //row 3: different email
        $addressRows[2]['_email'] = 'fakeemail.test@test.com';
        //row 4: different website
        $addressRows[3]['_website'] = 'admin';
        //row 5: different address id
        $addressRows[4]['_entity_id'] = '10000';
		//row 6: empty address id
		$addressRows[5]['_entity_id'] = '';

        //validation messages
        $successfulImport = array('validation' => array(
                'validation' => array("Checked rows: 1, checked entities: 1, invalid rows: 0, total errors: 0"),
                'success' => array(
                    "File is valid! To start import process press \"Import\" button  Import"
                )
            ),
            'import' => array(
                'success' => array('Import successfully done.')
            )
        );
        $customerNotFound = array('validation' => array(
                'error' => array("Customer with such email and website code doesn't exist in rows: 1"),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
                )
            )
        );
        $addressNotFound = array('validation' => array(
                'error' => array("Customer address for such customer doesn't exist in rows: 1"),
                'validation' => array(
                    "File is totally invalid. Please fix errors and re-upload file",
                    "Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
                )
            )
        );
		$emptyEntityId = array('validation' => array(
			'error' => array("Customer address id column is not specified in rows: 1"),
			'validation' => array(
				"File is totally invalid. Please fix errors and re-upload file",
				"Checked rows: 1, checked entities: 1, invalid rows: 1, total errors: 1",
			)
		)
		);

        return array(
            array($addressData[0], array($addressRows[0]), true, $successfulImport),
            array($addressData[1], array($addressRows[1]), true, $successfulImport),
            array($addressData[2], array($addressRows[2]), false, $customerNotFound),
            array($addressData[3], array($addressRows[3]), false, $customerNotFound),
            array($addressData[4], array($addressRows[4]), false, $addressNotFound),
			array($addressData[5], array($addressRows[5]), false, $emptyEntityId),
            );
    }
}