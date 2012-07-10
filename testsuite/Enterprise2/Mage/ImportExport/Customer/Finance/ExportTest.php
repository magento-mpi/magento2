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
 * Customer Finances Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
 */
class Enterprise2_Mage_ImportExport_Export_FinanceTest extends Mage_Selenium_TestCase
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
        $this->navigate('export');
    }

    /**
     * <p>Simple Export Finance file</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>3. Select new Export flow</p>
     * <p>4. Choose Customer Finance file to export</p>
     * <p>5. Click on the Continue button</p>
     * <p>6. Save file to your computer</p>
     * <p>7. Open it.</p>
     * <p>Expected: Check that among all customers your customer with attribute is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5491
     */
    public function simpleExportFinanceFile()
    {
        //Preconditions
        $this->navigate('manage_customers');
        $attrData = $this->loadDataSet('ImportExport', 'generic_customer_account');
        $this->customerHelper()->createCustomer($attrData);
        $this->addParameter('customer_first_last_name', $attrData['first_name'] . ' ' . $attrData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' =>'10011'));
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' =>'1002'));
        //Steps 1-4
        $this->admin('export');
		$this->importExportHelper()->chooseExportOptions('Customers', 'Magento 2.0 format', 'Customer Finances');
        $report = $this->ImportExportHelper()->export();
        $this->assertNotNull($this->importExportHelper()->lookForEntity('finance',
                array(
                    'email' => $attrData['email'],
                    'store_credit' => '10011',
                    'reward_points' => '1002'
                ),
                $report),
            "Customer with specific Store Credit and Reward Points not found in csv file");

    }
}