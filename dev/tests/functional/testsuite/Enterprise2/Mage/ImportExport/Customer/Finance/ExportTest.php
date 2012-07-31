<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Finances Export
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_Export_FinanceTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
    }

    /**
     * Simple Export Finance file
     * Steps
     * 1. Go to System -> Import/ Export -> Export
     * 2. Choose Customer Finance file to export
     * 3. Click on the Continue button
     * 4. Save file to your computer
     * 5. Open it.
     * Expected: Check that among all customers your customer with attribute is present
     *
     * @test
     * @TestlinkId TL-MAGE-5491
     */
    public function simpleExportFinanceFile()
    {
        //Preconditions
        $this->navigate('manage_customers');
        $attrData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($attrData);
        $this->addParameter('customer_first_last_name', $attrData['first_name'] . ' ' . $attrData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' =>'10011'));
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' =>'1002'));
        //Step 1
        $this->navigate('export');
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customer Finances');
        //Steps 3-4
        $report = $this->ImportExportHelper()->export();
        $this->assertNotNull($this->importExportHelper()->lookForEntity('finance',
                array(
                    '_email' => $attrData['email'],
                    'store_credit' => '10011',
                    'reward_points' => '1002'
                ),
                $report),
            "Customer with specific Store Credit and Reward Points not found in csv file");
    }
}