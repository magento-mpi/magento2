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
        $attrData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($attrData);
        $this->addParameter('customer_first_last_name', $attrData['first_name'] . ' ' . $attrData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' =>'10011'));
        $this->customerHelper()->openCustomer(array('email' => $attrData['email']));
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' =>'1002'));
        //Steps 1-4
        $this->navigate('export');
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