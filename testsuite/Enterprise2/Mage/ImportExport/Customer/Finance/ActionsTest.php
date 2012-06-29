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
 */
class Enterprise2_Mage_ImportExport_CustomActionsImportFinanceTest extends Mage_Selenium_TestCase
{
    static protected $customersData = array();
    public function setUpBeforeTests(){
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('manage_customers');
        for($i = 0; $i<3; $i++){
            $userData = $this->loadDataSet('ImportExport', 'generic_customer_account');
            $this->customerHelper()->createCustomer($userData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            self::$customersData[] = $userData;
        }
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
        $this->navigate('export');
    }

    /**
     * <p>Required columns</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import / Export -> Import</p>
     * <p>2. Select Entity Type: Customers</p>
     * <p>3. Select Export Format Version: Magento 2.0 format</p>
     * <p>4. Select Customers Entity Type: Customer Finances File</p>
     * <p>5. Choose file from precondition</p>
     * <p>6. Click on Check Data</p>
     * <p>7. Click on Import button</p>
     * <p>8. Open Customers -> Manage Customers</p>
     * <p>9. Open each of imported customers</p>
     * <p>After step 6</p>
     * <p>Verify that file is valid, the message 'File is valid!' is displayed</p>
     * <p>After step 7</p>
     * <p>Verify that import starting. The message 'Import successfully done.' is displayed</p>
     * <p>After step 9</p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5624
     */
    public function updateActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach($data as $customerData){
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ){
                $this->admin('manage_customers');
                $this->addParameter('customer_first_last_name', self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                if ($data[$i]['store_credit']!=''){
                    $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '1234'), true);
                }
                if ($data[$i]['reward_points']!=''){
                    $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '4321'), true);
                }
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['email'] == '%realEmail%'){
                $dataCsv[$i]['email'] = self::$customersData[$i]['email'];
            }
            $i++;
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Add/Update Complex Data',
            'Magento 2.0 format', 'Customer Finances');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($report));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($report));
    }
    public function importData()
    {
        return array(
          array(
            array(
               array(
                    'store_credit' => '',
                    'reward_points' => ''
                ),
                array(
                    'store_credit' => '200',
                    'reward_points' => '250'
                ),
                array(
                    'store_credit' => '300',
                    'reward_points' => '350'
                )
            ),
            array(
                array(
                    'email' => '%realEmail%',
                    '_website' => 'base',
                    '_finance_website' => 'base',
                    'store_credit' => '10',
                    'reward_points' => '20',
                    'action' => 'update'
                ),
                array(
                    'email' => 'wrongEmail@example.com',
                    '_website' => 'base',
                    '_finance_website' => 'base',
                    'store_credit' => '250',
                    'reward_points' => '300',
                    'action' => 'UPDATE'
                ),
                array(
                    'email' => '%realEmail%',
                    '_website' => 'base',
                    '_finance_website' => 'base',
                    'store_credit' => '0',
                    'reward_points' => '0',
                    'action' => 'Update'
                )
            )
          )
        );
    }
}