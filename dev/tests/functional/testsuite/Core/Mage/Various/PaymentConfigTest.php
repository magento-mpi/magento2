<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Various
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Impossible to save payment method configurations on the Default Config scope - MAGE-5774
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Core_Mage_Various_PaymentConfigTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Impossible to save payment method configurations on the Default Config scope</p>
     * <p>Verification of MAGE-5774</p>
     * <p> Steps:</p>
     *
     * <p>1. Log in to Backend;</p>
     * <p>2. Go to System -> Configuration -> Sales -> Payment Methods -> Saved CC (use Default Config scope);</p>
     * <p>3. Click on "Save" button;</p>
     * <p>4. Enter some numeric value to "Sort Order" field.</p>
     * <p>5. Click on "Save" button.</p>
     *
     * <p>Expected Result:</p>
     * <p>After step 3: Sort Order field should not be required field;</p>
     * <p>After step 5: payment method should be saved;</p>
     *
     * @test
     */
    public function paymentConfigVerification()
    {
        //Data
        $paymentMethodData = $this->loadDataSet('PaymentMethod', 'savedcc_without_3Dsecure',
            array('scc_sort_order' =>''));
        //Steps
        $this->systemConfigurationHelper()->configure($paymentMethodData);
        $paymentMethodData['tab_1']['configuration']['scc_sort_order'] = rand(1, 10);
        $this->systemConfigurationHelper()->configure($paymentMethodData);
    }
}