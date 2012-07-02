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
class Enterprise2_Mage_ImportExport_CustomActionsImportAddressTest extends Mage_Selenium_TestCase
{
    static protected $customersData = array();
    public function setUpBeforeTests(){
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('manage_customers');
        for($i = 0; $i<1; $i++){
            $userData = $this->loadDataSet('ImportExport', 'generic_customer_account');
            $userAddressData = $this->loadDataSet('ImportExport', 'generic_address');
            $userAddressData1 = $this->loadDataSet('ImportExport', 'generic_address');
            $this->customerHelper()->createCustomer($userData, $userAddressData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            $this->addParameter(
                'customer_first_last_name',
                $userData['first_name'] . ' ' . $userData['last_name']);
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            $this->customerHelper()->addAddress($userAddressData1);
            $this->customerHelper()->saveForm('save_customer');
            $this->customerHelper()->openCustomer(array('email' => $userData['email']));
            self::$customersData[] = array('email' => $userData['email'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                array($this->customerHelper()->isAddressPresent($userAddressData),
                    $this->customerHelper()->isAddressPresent($userAddressData1)));


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
     * <p>Custom import: update finance information</p>
     * <p>Need to verify that the customer finances information is updated if the action is "Update" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5689
     */
    public function updateActionImport(array $data)
    {
//        //Precondition: create 2 new customers
//        $i = 0;
//        foreach($data as $customerData){
//            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ){
//                $this->admin('manage_customers');
//                $this->addParameter(
//                    'customer_first_last_name',
//                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
//                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
//                if ($data[$i]['store_credit']!=''){
//                    $this->customerHelper()->updateStoreCreditBalance(
//                        array(
//                            'update_balance' => $customerData['store_credit']),
//                        true);
//                }
//                if ($data[$i]['reward_points']!=''){
//                    $this->customerHelper()->updateRewardPointsBalance(
//                        array(
//                            'update_balance' => $customerData['reward_points']),
//                        true);
//                }
//                $this->customerHelper()->saveForm('save_customer');
//                $this->assertMessagePresent('success', 'success_saved_customer');
//            }
//            if ($dataCsv[$i]['email'] == '%realEmail%'){
//                $dataCsv[$i]['email'] = self::$customersData[$i]['email'];
//            }
//            $i++;
//        }
//        $this->admin('import');
//        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
//            'Magento 2.0 format', 'Customer Finances');
//        //Step 5, 6, 7
//        $importResult = $this->importExportHelper()->import($dataCsv);
//        //Check import
//        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
//            . print_r($importResult));
//        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
//            . print_r($importResult));
//        $this->assertEquals(
//            "Customer with such email and website code doesn't exist in rows: 2",
//            $importResult['validation']['error'][0],
//            'Import has been finished with issues: ' . print_r($importResult));
//        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
//            . print_r($importResult));
//        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
//            . print_r($importResult));
//        //Verifying
//        $i = 0;
//        foreach($dataCsv as $customerData){
//             $this->admin('manage_customers');
//             if ($customerData['email'] == 'wrongEmail@example.com'){
//                 $this->assertFalse(
//                      $this->customerHelper()->isCustomerPresentInGrid(
//                          array(
//                              'email' =>$dataCsv[$i]['email'])
//                      ),
//                      'Customer was found!'
//                 );
//             } else {
//                 $this->addParameter(
//                     'customer_first_last_name',
//                     self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
//                 $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
//                 $this->assertEquals(
//                     $dataCsv[$i]['store_credit'],
//                     str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
//                     'Store credit balance is wrong');
//                 $this->assertEquals(
//                     $dataCsv[$i]['reward_points'],
//                     $this->customerHelper()->getRewardPointsBalance(),
//                     'Reward points balance is wrong');
//             }
//             $i++;
//        }
    }
    public function importUpdateData()
    {
        return array(
            array(array(array(
                '_website' => 'base',
                'region' => 'New York',
                'company' => '',
                'fax' => '',
                'middlename' => '',
                'prefix' =>'',
                '_address_default_billing_' => '',
                '_address_default_shipping_' => '',
                '_entity_id' => $this->generate('string', 10, ':digit:')
            )))
        );
    }
}