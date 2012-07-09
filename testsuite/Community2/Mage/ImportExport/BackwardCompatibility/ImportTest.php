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
* Customer Export
*
* @package     selenium
* @subpackage  tests
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*
* @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
* @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
* @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
*/
class Community2_Mage_ImportExport_Backward_Import_CustomerTest extends Mage_Selenium_TestCase
{
    protected static $customerData = array();
    protected static $addressData = array();

    /**
     * <p>Precondition:</p>
     * <p>Create new customer</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->admin('manage_customers');
        self::$customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        self::$addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer(self::$customerData, self::$addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
    }
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
     * <p>Validation Result block</p>
     * <p>Verify that Validation Result block will be displayed after checking data of import file</p>
     * <p>Precondition: at least one customer exists, one file is generated after export</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. In "Import Format Version" dropdown field choose "Magento 1.7 format" parameter</p>
     * <p>4. In "Import Behavior" dropdown field choose "Append Complex Data" parameter</p>
     * <p>5. Select file to import</p>
     * <p>6. Click "Check Data" button.</p>
     * <p>Expected: validation and success messages are correct</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1108
     */
    public function validationResultBlock()
    {
        //Precondition
        $this->admin('export');
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        $report = $this->importExportHelper()->export();
        //Step 1
        $this->admin('import');
        //Steps 2-4
        $this->importExportHelper()->chooseImportOptions('Customers', 'Append Complex Data',
            'Magento 1.7 format');
        //Step 5-6
        $importData = $this->importExportHelper()->import($report);
        //Verifying
        $this->assertEquals('Checked rows: ' . count($report) . ', checked entities: '
                . count($report)
                . ', invalid rows: 0, total errors: 0', $importData['validation']['validation'][0],
            'Validation message is not correct');
        $this->assertEquals('File is valid! To start import process press "Import" button  Import',
            $importData['validation']['success'][0], 'Success message is not correct');
    }
 }