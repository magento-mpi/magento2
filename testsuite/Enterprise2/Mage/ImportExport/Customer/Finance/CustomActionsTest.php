<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Finances Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_CustomActions_FinanceTest extends Mage_Selenium_TestCase
{
    static protected $_customersData = array();

    public function setUpBeforeTests()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        for ($i = 0; $i <5; $i++) {
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $this->customerHelper()->createCustomer($userData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            self::$_customersData[] = $userData;
        }
    }
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
    }

    /**
     * Custom import: update finance information
     * Need to verify that the customer finances information is updated if the action is "Update" in the csv file
     * After steps
     * Verify that all Customers finance information was imported
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5689
     */
    public function updateActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData) {
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ) {
                $this->navigate('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($data[$i]['store_credit']!='') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                }
                if ($data[$i]['reward_points']!='') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array(
                            'update_balance' => $customerData['reward_points']),
                        true);
                }
                $this->customerHelper()->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['_email'] == '<realEmail>') {
                $dataCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
            $i++;
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 2",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult, true));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData) {
            $this->navigate('manage_customers');
            if ($customerData['_email'] == 'wrongEmail@example.com') {
                 $this->assertFalse(
                      $this->customerHelper()->isCustomerPresentInGrid(
                          array(
                              'email' =>$dataCsv[$i]['_email'])
                      ),
                      'Customer was found!'
                 );
            } else {
                 $this->addParameter(
                     'customer_first_last_name',
                     self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                 $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
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
     * Custom import: not recognized or empty action
     * Verify that the customer finances information is updated if the action is empty or not recognized in csv file
     * After steps
     * Verify that all Customers finance information was imported
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5691
     */
    public function emptyActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData) {
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ) {
                $this->navigate('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($data[$i]['store_credit']!='') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                }
                if ($data[$i]['reward_points']!='') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array(
                            'update_balance' => $customerData['reward_points']),
                        true);
                }
                $this->customerHelper()->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['_email'] == '<realEmail>') {
                $dataCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
            $i++;
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 2",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult, true));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData) {
            $this->navigate('manage_customers');
            if ($customerData['_email'] == 'wrongEmail@example.com') {
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array(
                            'email' =>$dataCsv[$i]['_email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->addParameter(
                    'customer_first_last_name',
                    self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
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
     * Custom import: delete finance information
     * Need to verify that the customer finances information is cleared if the action is "Delete" in the csv file
     * After steps
     * Verify that all Customers finance information was imported
     *
     * @test
     * @dataProvider importDeleteData
     * @TestlinkId TL-MAGE-5690
     */
    public function deleteActionImport(array $data, array $dataCsv)
    {
        //Precondition: create 2 new customers
        $i = 0;
        foreach ($data as $customerData) {
            if ($data[$i]['store_credit']!='' || $data[$i]['reward_points']!='' ) {
                $this->navigate('manage_customers');
                $this->addParameter(
                    'customer_first_last_name',
                    self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($data[$i]['store_credit']!='') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array(
                            'update_balance' => $customerData['store_credit']),
                        true);
                } else {
                    $data[$i]['store_credit'] = str_replace('$', '', $this->customerHelper()->getStoreCreditBalance());
                }
                if ($data[$i]['reward_points']!='') {
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
            if ($dataCsv[$i]['_email'] == '<realEmail>') {
                $dataCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
            $i++;
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('error', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertEquals(
            "Customer with such email and website code doesn't exist in rows: 6",
            $importResult['validation']['error'][0],
            'Import has been finished with issues: ' . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult, true));
        //Verifying
        $i = 0;
        foreach ($dataCsv as $customerData) {
            $this->navigate('manage_customers');
            if ($customerData['_email'] == 'wrongEmail@example.com') {
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array(
                            'email' =>$dataCsv[$i]['_email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->addParameter(
                    'customer_first_last_name',
                    self::$_customersData[$i]['first_name'] . ' ' . self::$_customersData[$i]['last_name']);
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if (strtolower($dataCsv[$i]['_action']) == 'delete') {
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
    /**
     * Deleting customer finances with wrong or not cpecified _finance_website
     * Preconditions:
     * 1. Create two customers in Customers-> Manage Customers
     * 2. Update for both customers "Store Credit" and "Reward Points"
     * 3. Create csv file with empty _finance_website for customer1, with incorrect  _finance_website for customer2
     * Steps
     * 1. In System -> Import/ Export -> Import in drop-down "Entity Type" select "Customer Finances"
     * 2. Select "Delete" in selector "Import Behavior"
     * 3. Choose file from precondition
     * 4. Press "Check Data"
     * 5. Open Customers-> Manage Customers
     * Expected: After step 4 the message 'File is totaly invalid' is appeared
     * Expected: After step 6 the finances for both customers aren't deleted
     *
     * @test
     * @dataProvider importFinance
     * @TestlinkId TL-MAGE-5717
     */
    public function deleteWrongFinanceWebsite($data)
    {

        //Create Customer1
        $this->navigate('manage_customers');
        $userDataOne = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataOne);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userDataTwo = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userDataTwo);
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 1
        $this->addParameter('customer_first_last_name', $userDataOne['first_name'] . ' ' . $userDataOne['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
        $userDataOne['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
        $userDataOne['update_balance'] = '150';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->addParameter('customer_first_last_name', $userDataTwo['first_name'] . ' ' . $userDataTwo['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
        $userDataTwo['update_balance'] = '200';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
        $userDataTwo['update_balance'] = '250';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userDataOne['email'];;

        $data[1]['_email'] = $userDataTwo['email'];
        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Delete Entities');
        //Steps 3-4
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayNotHasKey('import', $report,
            'Import has been finished with issues:');
        $this->assertArrayHasKey('error', $report['validation'],
            'Import has been finished with issues:');
        $this->assertEquals('Finance information website is not specified in rows: 1',
            $report['validation']['error'][0]);
        $this->assertEquals('Invalid value in Finance information website column in rows: 2',
            $report['validation']['error'][1]);
        //Step 5
        $this->navigate('manage_customers');
        //Step 6. First Customer
        $this->addParameter('customer_first_last_name', $userDataOne['first_name'] . ' ' . $userDataOne['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataOne['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
        //Step 6. Second Customer
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataTwo['first_name'] . ' ' . $userDataTwo['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $userDataTwo['email']));
        //Verify customer account
        $this->assertEquals('$200.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('250', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
    }
    public function importFinance()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array(
                            'store_credit' => '100',
                            'reward_points' => '150',
                            '_finance_website' => '',
                            '_action' => 'delete'
                        )
                    ),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                        array(
                            'store_credit' => '200',
                            'reward_points' => '250',
                            '_finance_website' => $this->generate('string', 30, ':digit:'),
                            '_action' => 'delete'
                        )
                    )
                )
            )
        );
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
                    '_email' => '<realEmail>',
                    'store_credit' => '10',
                    'reward_points' => '20',
                    '_action' => 'update'
                )),
                $this->loadDataSet('ImportExport', 'generic_finance_csv',
                array(
                    '_email' => 'wrongEmail@example.com',
                    'store_credit' => '250',
                    'reward_points' => '300',
                    '_action' => 'UPDATE'
                )),
                $this->loadDataSet('ImportExport', 'generic_finance_csv',
                array(
                    '_email' => '<realEmail>',
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
                        '_email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => ''
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'delete me'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => '<realEmail>',
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
                        '_email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => '<realEmail>',
                        'store_credit' => '1',
                        'reward_points' => '1',
                        '_action' => 'DeLeTe'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => '<realEmail>',
                        'store_credit' => '101',
                        'reward_points' => '201',
                        '_action' => 'Del'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => '<realEmail>',
                        'store_credit' => '',
                        'reward_points' => '1',
                        '_action' => 'Delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => '<realEmail>',
                        'store_credit' => '1',
                        'reward_points' => '',
                        '_action' => 'DELETE'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv',
                    array(
                        '_email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'Delete'
                    ))
                )
            )
        );
    }
}