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
class Community2_Mage_ImportExport_EmptyValues_AddressTest extends Mage_Selenium_TestCase
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
     * <p>Empty values for existing attributes in csv for Customer Addresses</p>
     * <p>Preconditions:</p>
     * <p>1. Customer is created. Address is added. Company address attribute has some value</p>
     * <p>2. CSV file prepared that contains existing customer address info where Company value is empty</p>
     * <p>Steps</p>
     * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Addresses"</p>
     * <p>2. Select "Add/Update Complex Data" in selector "Import Behavior"</p>
     * <p>3. Choose file from precondition</p>
     * <p>4. Press "Check Data"</p>
     * <p>5. Press "Import" button</p>
     * <p>6. Open Customers-> Manage Customers</p>
     * <p>7. Open customer from precondition</p>
     * <p>Expected: Verify that Company hasn't been changed or removed in "Addresses" tab</p>
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5640
     */
    public function emptyValuesAttributesInCsv($data)
    {
        //Precondition: create customer, add address
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($userData, $addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('addresses');
        $addressId = $this->customerHelper()->isAddressPresent($addressData);
        $this->customerHelper()->fillForm(array('company' => 'Test_Company'));
        $addressData['company'] = 'Test_Company';
        $this->saveForm('save_customer');

        $data[0]['_email'] = $userData['email'];
        $data[0]['_entity_id'] = $addressId;
        $data[0]['city'] = $addressData['city'];
        $data[0]['country_id'] = 'US';
        $data[0]['postcode'] = $addressData['zip_code'];
        $data[0]['street'] = $addressData['street_address_line_1'] . "\n" . $addressData['street_address_line_2'];
        $data[0]['telephone'] = $addressData['telephone'];
        $data[0]['firstname'] = $addressData['first_name'];
        $data[0]['lastname'] = $addressData['last_name'];

        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Addresses', 'Add/Update Complex Data');
        //Steps 3-5
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:');
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:');
        //Step 6
        $this->navigate('manage_customers');
        //Step 7
        $this->addParameter('customer_first_last_name', $userData['first_name'] . ' ' . $userData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        //Verify Customer Address
        $this->openTab('addresses');
        $addressData['state'] = $data[0]['region'];
        $this->customerHelper()->isAddressPresent($addressData);
        $this->assertTrue($this->verifyForm(array('company' => 'Test_Company')),
            'Existent customer has been updated');
    }

    public function importData()
    {
        return array(
            array(
                array
                        ($this->loadDataSet('ImportExport','generic_address_csv',
                                array(
                                        '_website' => 'base',
                                        'region' => 'New York',
                                        'company' => '',
                                        'fax' => '',
                                        'middlename' => '',
                                        'prefix' =>'',
                                        '_address_default_billing_' => '',
                                        '_address_default_shipping_' => '',
                                        '_entity_id' => $this->generate('string', 10, ':digit:')
                                    )
                                )
                        )
                )
        );
    }
}