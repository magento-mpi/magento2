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
        for ($i = 0; $i<5; $i++){
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
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
     * <p>Custom import: update finance information</p>
     * <p>Need to verify that the customer finances information is updated if the action is "Update" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5689
     */
    public function updateActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData){
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ){
                $this->admin('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                if ($data[$i]['store_credit']!=''){
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                }
                if ($data[$i]['reward_points']!=''){
                    $this->customerHelper()->updateRewardPointsBalance(
                        array(
                            'update_balance' => $customerData['reward_points']),
                        true);
                }
                $this->customerHelper()->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['email'] == '<realEmail>'){
                $dataCsv[$i]['email'] = self::$customersData[$i]['email'];
            }
            $i++;
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Finances');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 2",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData){
             $this->admin('manage_customers');
             if ($customerData['email'] == 'wrongEmail@example.com'){
                 $this->assertFalse(
                      $this->customerHelper()->isCustomerPresentInGrid(
                          array(
                              'email' =>$dataCsv[$i]['email'])
                      ),
                      'Customer was found!'
                 );
             } else {
                 $this->addParameter(
                     'customer_first_last_name',
                     self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                 $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                 $this->assertEquals(
                     $dataCsv[$i]['store_credit'],
                     str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
                     'Store credit balance is wrong');
                 $this->assertEquals(
                     $dataCsv[$i]['reward_points'],
                     $this->customerHelper()->getRewardPointsBalance(),
                     'Reward points balance is wrong');
             }
             $i++;
        }
    }
    /**
     * <p>Custom import: not recognized or empty action</p>
     * <p>Need to verify that the customer finances information is updated if the action is "Update" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5691
     */
    public function emptyActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData){
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ){
                $this->admin('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                if ($data[$i]['store_credit']!=''){
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                }
                if ($data[$i]['reward_points']!=''){
                    $this->customerHelper()->updateRewardPointsBalance(
                        array(
                            'update_balance' => $customerData['reward_points']),
                        true);
                }
                $this->customerHelper()->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['email'] == '<realEmail>'){
                $dataCsv[$i]['email'] = self::$customersData[$i]['email'];
            }
            $i++;
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Finances');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 2",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData){
            $this->admin('manage_customers');
            if ($customerData['email'] == 'wrongEmail@example.com'){
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array(
                            'email' =>$dataCsv[$i]['email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->addParameter(
                    'customer_first_last_name',
                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                $this->assertEquals(
                    $dataCsv[$i]['store_credit'],
                    str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
                    'Store credit balance is wrong');
                $this->assertEquals(
                    $dataCsv[$i]['reward_points'],
                    $this->customerHelper()->getRewardPointsBalance(),
                    'Reward points balance is wrong');
            }
            $i++;
        }
    }
    /**
     * <p>Custom import: delete finance information</p>
     * <p>Need to verify that the customer finances information is cleared if the action is "Delete" in the csv file</p>
     * <p>After steps </p>
     * <p>Verify that all Customers finance information was imported</p>
     *
     * @test
     * @dataProvider importDeleteData
     * @TestlinkId TL-MAGE-5690
     */
    public function deleteActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData){
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ){
                $this->admin('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                if ($data[$i]['store_credit']!=''){
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                } else {
                    $data[$i]['store_credit'] = str_replace('$', '', $this->customerHelper()->getStoreCreditBalance());
                }
                if ($data[$i]['reward_points']!=''){
                    $this->customerHelper()->updateRewardPointsBalance(
                        array(
                            'update_balance' => $customerData['reward_points']),
                        true);
                } else {
                    $data[$i]['reward_points'] = $this->customerHelper()->getRewardPointsBalance();
                }
                $this->customerHelper()->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['email'] == '<realEmail>'){
                $dataCsv[$i]['email'] = self::$customersData[$i]['email'];
            }
            $i++;
        }
        $this->admin('import');
        $this->importExportHelper()->chooseImportOptions('Customers', 'Custom Action',
            'Magento 2.0 format', 'Customer Finances');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 6",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData){
            $this->admin('manage_customers');
            if ($customerData['email'] == 'wrongEmail@example.com'){
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array(
                            'email' =>$dataCsv[$i]['email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->addParameter(
                    'customer_first_last_name',
                    self::$customersData[$i]['first_name'] . ' ' . self::$customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$customersData[$i]['email']));
                if (strtolower($dataCsv[$i]['_action']) == 'delete'){
                    $currentBalance = str_replace('$', '', $this->customerHelper()->getStoreCreditBalance());
                    $this->assertTrue($currentBalance == '0' || $currentBalance == 'No records found.',
                        'Store credit balance is wrong ' . $i);
                    $currentBalance = $this->customerHelper()->getRewardPointsBalance();
                    $this->assertTrue($currentBalance == '0' || $currentBalance == 'No records found.',
                        'Reward points balance is wrong ' . $i);
                } else {
                    $this->assertEquals(
                        $dataCsv[$i]['store_credit'],
                        str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
                        'Store credit balance is wrong ' . $i);
                    $this->assertEquals(
                        $dataCsv[$i]['reward_points'],
                        $this->customerHelper()->getRewardPointsBalance(),
                        'Reward points balance is wrong ' . $i);
                }
            }
            $i++;
        }
    }
    public function importUpdateData()
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
                $this->loadDataSet('ImportExport', 'generic_finance_csv',
                array(
                    'email' => '<realEmail>',
                    'store_credit' => '10',
                    'reward_points' => '20',
                    '_action' => 'update'
                )),
                $this->loadDataSet('ImportExport', 'generic_finance_csv',
                array(
                    'email' => 'wrongEmail@example.com',
                    'store_credit' => '250',
                    'reward_points' => '300',
                    '_action' => 'UPDATE'
                )),
                $this->loadDataSet('ImportExport', 'generic_finance_csv',
                array(
                    'email' => '<realEmail>',
                    'store_credit' => '0',
                    'reward_points' => '0',
                    '_action' => 'Update'
                ))
            )
          )
        );
    }
    public function importEmptyData()
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
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => ''
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'delete me'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '0',
                        'reward_points' => '0',
                        '_action' => 'test action'
                    ))
                )
            )
        );
    }
    public function importDeleteData()
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
                    ),
                    array(
                        'store_credit' => '',
                        'reward_points' => '350'
                    ),
                    array(
                        'store_credit' => '300',
                        'reward_points' => ''
                    )
                ),
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '1',
                        'reward_points' => '1',
                        '_action' => 'DeLeTe'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '101',
                        'reward_points' => '201',
                        '_action' => 'Del'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '',
                        'reward_points' => '1',
                        '_action' => 'Delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => '<realEmail>',
                        'store_credit' => '1',
                        'reward_points' => '',
                        '_action' => 'DELETE'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        'email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'Delete'
                    ))
                )
            )
        );
    }
}