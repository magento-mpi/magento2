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
class Community2_Mage_ImportExport_CustomerImportTest extends Mage_Selenium_TestCase
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
     * <p>Export Settings General View</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Import</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "New Import" fromat</p>
     * <p>Expected: dropdowns contain correct values</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5615
     */
    public function importSettingsGeneralView()
    {
        //Verifying
        $entityTypes = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'entity_type') . '/option',
            'text');
        $this->assertEquals(array(
                '-- Please Select --',
                'Products',
                'Customers'
            ), $entityTypes,
            'Entity Type dropdown contains incorrect values');
        $entityBehavior = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_behavior') . '/option',
            'text');
        $this->assertEquals(array(
                '-- Please Select --',
                'Append Complex Data',
                'Replace Existing Complex Data',
                'Delete Entities'
             ), $entityBehavior,
            'Import Behavior dropdown contains incorrect values');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_file_version'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_file_version') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --', 'Magento 1.7 format', 'Magento 2.0 format'),
            $exportFileVersion,
            'Import File Version dropdown contains incorrect values');
        //Step 3
        $this->fillDropdown('import_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_customer_entity'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'import_customer_entity') . '/option',
            'text');
        $this->assertEquals($this->importExportHelper()->getCustomerEntityType(),
            $exportFileVersion,
            'Customer Entity Type dropdown contains incorrect values');
        $this->assertTrue($this->controlIsVisible('field','file_to_import'),
            'File to Import field is missing');
    }

    /**
     * @dataProvider importData
     * @test
     */
    public function simpleImport($data)
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_behavior'));
        $this->fillDropdown('import_behavior', 'Append Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('dropdown','import_file_version'));
        $this->fillDropdown('import_file_version', 'Magento 1.7 format');
        $report = $this->importExportHelper()->import($data);
    }

    public function importData()
    {
        return array(
            array(array(array(
                'email' => 'sdfsdf@qweqwe.cc',
                '_website' => 'admin',
                '_store' => 'admin',
                'attr_ainalkiudfyisqgt' => '',
                'attr_hkuhj' => '',
                'attr_lqkkk' => '',
                'attr_ltavp' => '',
                'attr_sntecahafewtpbxo' => '',
                'attr_vzcmj' => '',
                'attr_xolge' => '',
                'attr_zsjyqshlvqousmdh' => '',
                'confirmation' => '',
                'created_at' => '01.06.2012 14:35',
                'created_in' => 'Admin',
                'default_billing' => '',
                'default_shipping' => '',
                'disable_auto_group_change' => '0',
                'dob' => '',
                'firstname' => 'sdfsdfsd',
                'gender' => '',
                'group_id' => '1',
                'lastname' => 'sdfsdfs',
                'middlename' => '',
                'password_hash' => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                'prefix' => '',
                'reward_update_notification' => '1',
                'reward_warning_notification' => '1',
                'rp_token' => '',
                'rp_token_created_at' => '',
                'store_id' => '0',
                'suffix' => '',
                'taxvat' => '',
                'website_id' => '0',
                'password' => ''
            )))
        );
    }
}
