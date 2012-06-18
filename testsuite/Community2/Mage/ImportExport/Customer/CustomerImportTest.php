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
     * @dataProvider importData
     * @test
     */
    public function simpleImport($data)
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'import_behavior'));
        $this->fillDropdown('import_behavior', 'Append Complex Data');
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
    public function importDataCsv()
    {
        return array(
            array(array(array(

                'email' => 'test_admin_nhecx@unknown-domain.com',
                '_website' => 'base',
                '_store' => 'admin',
                'confirmation' => '',
                'created_at' => "2012-06-14 16:35:03",
                'created_in' => 'Admin',
                'disable_auto_group_change' => '0',
                'dob' => '',
                'firstname' => 'first_fegvq',
                'gender' => 'Female',
                'group_id' => '1',
                'lastname' => 'last_uxjnf',
                'middlename' => 'middle_xlkon',
                'password_hash' => '6fd5584d5ef3ec324784aceeafd4f2ddc7ca930580d1ccea793e7012209af4b8:xH',
                'prefix' => 'Mrs.',
                'reward_update_notification' => '1',
                'reward_warning_notification' => '1',
                'rp_token' => '',
                'rp_token_created_at' => '',
                'store_id' => '0',
                'suffix' => '',
                'taxvat' => '',
                'website_id' => '1',
                'password' => '',
                '_address_city' => "Culver City",
                '_address_company' => 'Magento',
                '_address_country_id' => 'US',
                '_address_fax' => '530-918-3581',
                '_address_firstname' => "Female First Name",
                '_address_lastname' => "Female Last Name",
                '_address_middlename' => "Female Middle Name",
                '_address_postcode' => '90232',
                '_address_prefix' => 'Prefix',
                '_address_region' => 'California',
                '_address_street' => "10441 Jefferson Blvd\nSuite 200",
                '_address_suffix' => 'Suffix',
                '_address_telephone' => '530-918-3581',
                '_address_vat_id' => '1',
                '_address_default_billing_' => '1',
                '_address_default_shipping_' => '1'
            )))
        );
    }
    /**
     * @dataProvider importDataCsv
     * @test
     */
    public function generateImport($data)
    {
        //generate records
        //Make tmp file
        $tempFile = $this->_testConfig->getHelper('config')->getLogDir() . DIRECTORY_SEPARATOR .
            'customer_' . date('Ymd_His') . '.csv';
        $handle = fopen($tempFile, 'w+');
        for ($i = 1; $i <= 100000; $i++) {
                $data[0]['email'] = 'test_' . $this->generate('string', 6) . '@' . $this->generate('string', 6) . '-domain.com';
                $dataCsv[] = $data[0];
            }
            $report = $this->importExportHelper()->arrayToCsv($dataCsv);
            fwrite($handle, $report);
        fclose($handle);
    }

}
