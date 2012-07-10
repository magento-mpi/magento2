<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Addresses Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
 * @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
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
 * <p>1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customers"</p>
 * <p>2. Select "Add/Update Complex Data" in selector "Import Behavior"</p>
 * <p>3. Select "Magento 2.0 format"</p>
 * <p>4. Select "Customer Addresses"</p>
 * <p>5. Choose file from precondition</p>
 * <p>6. Press "Check Data"</p>
 * <p>7. Press "Import" button</p>
 * <p>8. Open Customers-> Manage Customers</p>
 * <p>9. Open customer from precondition</p>
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

    //Step 1
    $this->admin('import');
    $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
        'Magento 2.0 format', 'Customer Addresses');
    //Step 5, 6, 7
    $report = $this->importExportHelper()->import($data);
    //Check import
    $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:');
    $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:');
    //Step 8
    $this->navigate('manage_customers');
    //Step 9
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