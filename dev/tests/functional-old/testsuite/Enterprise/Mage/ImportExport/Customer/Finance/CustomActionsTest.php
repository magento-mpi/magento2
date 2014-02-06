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
class Enterprise_Mage_ImportExport_Customer_Finance_CustomActionsTest extends Mage_Selenium_TestCase
{
    static protected $_customersData = array();

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        for ($i = 0; $i < 5; $i++) {
            $userData = $this->loadDataSet('Customers', 'generic_customer_account');
            $this->customerHelper()->createCustomer($userData);
            $this->assertMessagePresent('success', 'success_saved_customer');
            self::$_customersData[] = $userData;
        }
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Custom import: update finance information
     * Need to verify that the customer finances information is updated if the action is "Update" in the csv file
     *
     * @test
     * @dataProvider importUpdateData
     * @TestlinkId TL-MAGE-5689
     */
    public function updateActionImport(array $updateData, array $dataUpdateCsv)
    {
        //Precondition: create 2 new customers
        for ($i = 0; $i < count($updateData); $i++) {
            if ($updateData[$i]['store_credit'] != '' || $updateData[$i]['reward_points'] != '') {
                $this->navigate('manage_customers');
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($updateData[$i]['store_credit'] != '') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array('update_balance' => $updateData[$i]['store_credit']), true
                    );
                }
                if ($updateData[$i]['reward_points'] != '') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array('update_balance' => $updateData[$i]['reward_points']), true
                    );
                }
                $this->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataUpdateCsv[$i]['_email'] == '<realEmail>') {
                $dataUpdateCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataUpdateCsv);
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
        for ($i = 0; $i < count($dataUpdateCsv); $i++) {
            $this->navigate('manage_customers');
            if ($dataUpdateCsv[$i]['_email'] == 'wrongEmail@example.com') {
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array('email' => $dataUpdateCsv[$i]['_email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                $this->assertEquals(
                    $dataUpdateCsv[$i]['store_credit'],
                    str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
                    'Store credit balance is wrong'
                );
                $this->assertEquals(
                    $dataUpdateCsv[$i]['reward_points'],
                    $this->customerHelper()->getRewardPointsBalance(),
                    'Reward points balance is wrong'
                );
            }
        }
    }

    /**
     * Custom import: not recognized or empty action
     * Verify that the customer finances information is updated if the action is empty or not recognized in csv file
     *
     * @test
     * @dataProvider importEmptyData
     * @TestlinkId TL-MAGE-5691
     */
    public function emptyActionImport(array $emptyData, array $dataEmptyCsv)
    {
        //Precondition: create 2 new customers
        for ($i = 0; $i < count($emptyData); $i++) {
            if ($emptyData[$i]['store_credit'] != '' || $emptyData[$i]['reward_points'] != '') {
                $this->navigate('manage_customers');
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($emptyData[$i]['store_credit'] != '') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array('update_balance' => $emptyData[$i]['store_credit']), true
                    );
                }
                if ($emptyData[$i]['reward_points'] != '') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array('update_balance' => $emptyData[$i]['reward_points']), true
                    );
                }
                $this->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataEmptyCsv[$i]['_email'] == '<realEmail>') {
                $dataEmptyCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataEmptyCsv);
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
        for ($i = 0; $i < count($dataEmptyCsv); $i++) {
            $this->navigate('manage_customers');
            if ($dataEmptyCsv[$i]['_email'] == 'wrongEmail@example.com') {
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array('email' => $dataEmptyCsv[$i]['_email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                $this->assertEquals(
                    $dataEmptyCsv[$i]['store_credit'],
                    str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
                    'Store credit balance is wrong');
                $this->assertEquals(
                    $dataEmptyCsv[$i]['reward_points'],
                    $this->customerHelper()->getRewardPointsBalance(),
                    'Reward points balance is wrong');
            }
        }
    }

    /**
     * Custom import: delete finance information
     * Need to verify that the customer finances information is cleared if the action is "Delete" in the csv file
     *
     * @test
     * @dataProvider importDeletePositiveData
     * @TestlinkId TL-MAGE-5690
     */
    public function deleteActionImportPositive(array $deletePositiveData, array $dataCsv)
    {
        //Precondition:
        $this->navigate('manage_customers');
        for ($i = 0; $i < count($deletePositiveData); $i++) {
            $dataCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            if ($deletePositiveData[$i]['store_credit'] != '' || $deletePositiveData[$i]['reward_points'] != '') {
                $this->customerHelper()->openCustomer(array('email' => $dataCsv[$i]['_email']));
                if ($deletePositiveData[$i]['store_credit'] != '') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array('update_balance' => $deletePositiveData[$i]['store_credit']), true
                    );
                } else {
                    $deletePositiveData[$i]['store_credit'] =
                        str_replace('$', '', $this->customerHelper()->getStoreCreditBalance());
                }
                if ($deletePositiveData[$i]['reward_points'] != '') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array('update_balance' => $deletePositiveData[$i]['reward_points']), true
                    );
                } else {
                    $deletePositiveData[$i]['reward_points'] = $this->customerHelper()->getRewardPointsBalance();
                }
                $this->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
        }
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customer Finances', 'Custom Action');
        //Step 5, 6, 7
        $importResult = $this->importExportHelper()->import($dataCsv);
        //Check import
        $this->assertArrayHasKey('validation', $importResult, 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['validation'], 'Import has been finished without issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('import', $importResult, 'Import has been finished with issues: '
            . print_r($importResult, true));
        $this->assertArrayHasKey('success', $importResult['import'], 'Import has been finished with issues: '
            . print_r($importResult, true));
        //Verifying
        for ($i = 0; $i < count($dataCsv); $i++) {
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer(array('email' => $dataCsv[$i]['_email']));
            $currentBalance = $this->customerHelper()->getStoreCreditBalance();
            $this->assertTrue($currentBalance == '$0.00' || $currentBalance == "We couldn't  find any records.",
                'Store credit balance is wrong ' . $currentBalance);
            $currentBalance = $this->customerHelper()->getRewardPointsBalance();
            $this->assertTrue($currentBalance == '0' || $currentBalance == 'No records found.',
                'Reward points balance is wrong ' . $currentBalance);
        }
    }

    /**
     * Custom import: delete finance information
     * Need to verify that the customer finances information is cleared if the action is "Delete" in the csv file
     *
     * @test
     * @dataProvider importDeleteNegativeData
     * @TestlinkId TL-MAGE-5690
     */
    public function deleteActionImportNegative(array $deleteNegativeData, array $dataCsv)
    {
        //Precondition: create 2 new customers
        for ($i = 0; $i < count($deleteNegativeData); $i++) {
            if ($deleteNegativeData[$i]['store_credit'] != '' || $deleteNegativeData[$i]['reward_points'] != '') {
                $this->navigate('manage_customers');
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
                if ($deleteNegativeData[$i]['store_credit'] != '') {
                    $this->customerHelper()->updateStoreCreditBalance(
                        array('update_balance' => $deleteNegativeData[$i]['store_credit']), true
                    );
                } else {
                    $deleteNegativeData[$i]['store_credit'] =
                        str_replace('$', '', $this->customerHelper()->getStoreCreditBalance());
                }
                if ($deleteNegativeData[$i]['reward_points'] != '') {
                    $this->customerHelper()->updateRewardPointsBalance(
                        array('update_balance' => $deleteNegativeData[$i]['reward_points']), true
                    );
                } else {
                    $deleteNegativeData[$i]['reward_points'] = $this->customerHelper()->getRewardPointsBalance();
                }
                $this->saveForm('save_customer');
                $this->assertMessagePresent('success', 'success_saved_customer');
            }
            if ($dataCsv[$i]['_email'] == '<realEmail>') {
                $dataCsv[$i]['_email'] = self::$_customersData[$i]['email'];
            }
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
        for ($i = 0; $i < count($dataCsv); $i++) {
            $this->navigate('manage_customers');
            if ($dataCsv[$i]['_email'] == 'wrongEmail@example.com') {
                $this->assertFalse(
                    $this->customerHelper()->isCustomerPresentInGrid(
                        array('email' => $dataCsv[$i]['_email'])
                    ),
                    'Customer was found!'
                );
            } else {
                $this->customerHelper()->openCustomer(array('email' => self::$_customersData[$i]['email']));
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
    }

    /**
     * Deleting customer finances with wrong or not specified _finance_website
     *
     * @test
     * @dataProvider importFinance
     * @TestlinkId TL-MAGE-5717
     */
    public function deleteWrongFinanceWebsite($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData[0] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Update Customer 1
        $this->customerHelper()->openCustomer(array('email' => $userData[0]['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '100'));
        $userData[0]['update_balance'] = '100';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData[0]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '150'));
        $userData[0]['update_balance'] = '150';
        $this->assertMessagePresent('success', 'success_saved_customer');

        //Update Customer 2
        $this->customerHelper()->openCustomer(array('email' => $userData[1]['email']));

        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => '200'));
        $userData[1]['update_balance'] = '200';
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData[1]['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => '250'));
        $userData[1]['update_balance'] = '250';
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['_email'] = $userData[0]['email'];;
        $data[1]['_email'] = $userData[1]['email'];

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
        $this->customerHelper()->openCustomer(array('email' => $userData[0]['email']));
        //Verify customer account
        $this->assertEquals('$100.00', $this->customerHelper()->getStoreCreditBalance(),
            'Store Credit balance is deleted');
        $this->assertEquals('150', $this->customerHelper()->getRewardPointsBalance(),
            'Reward Points Balance is deleted');
        //Step 6. Second Customer
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userData[1]['email']));
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
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '100',
                        'reward_points' => '150',
                        '_finance_website' => '',
                        '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '200',
                        'reward_points' => '250',
                        '_finance_website' => $this->generate('string', 30, ':digit:'),
                        '_action' => 'delete'
                    ))
                )
            )
        );
    }

    public function importUpdateData()
    {
        return array(
            array(
                array(
                    array('store_credit' => '', 'reward_points' => ''),
                    array('store_credit' => '200', 'reward_points' => '250'),
                    array('store_credit' => '300', 'reward_points' => '350')
                ),
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => 'update'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'UPDATE'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
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
                    array('store_credit' => '', 'reward_points' => ''),
                    array('store_credit' => '200', 'reward_points' => '250'),
                    array('store_credit' => '300', 'reward_points' => '350')
                ),
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => '<realEmail>',
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => ''
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => 'wrongEmail@example.com',
                        'store_credit' => '250',
                        'reward_points' => '300',
                        '_action' => 'delete me'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => '<realEmail>',
                        'store_credit' => '0',
                        'reward_points' => '0',
                        '_action' => 'test action'
                    ))
                )
            )
        );
    }

    public function importDeletePositiveData()
    {
        return array(
            array(
                array(
                    array('store_credit' => '', 'reward_points' => ''),
                    array('store_credit' => '200', 'reward_points' => '250'),
                    array('store_credit' => '', 'reward_points' => '350'),
                    array('store_credit' => '300', 'reward_points' => '')
                ),
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '10',
                        'reward_points' => '20',
                        '_action' => 'delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '1',
                        'reward_points' => '1',
                        '_action' => 'DeLeTe'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '',
                        'reward_points' => '1',
                        '_action' => 'Delete'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        'store_credit' => '1',
                        'reward_points' => '',
                        '_action' => 'DELETE'
                    )),
                )
            )
        );
    }

    public function importDeleteNegativeData()
    {
        return array(
            array(
                array(
                    array('store_credit' => '300', 'reward_points' => '350'),
                ),
                array(
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                        '_email' => '<realEmail>',
                        'store_credit' => '101',
                        'reward_points' => '201',
                        '_action' => 'Del'
                    )),
                    $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
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
