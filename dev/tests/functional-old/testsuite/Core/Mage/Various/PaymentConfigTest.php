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
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Impossible to save payment method configurations on the Default Config scope</p>
     * <p>Verification of MAGE-5774</p>
     *
     * @test
     */
    public function paymentConfigVerification()
    {
        //Data
        $paymentMethod = $this->loadDataSet('PaymentMethod', 'savedcc_without_3Dsecure',
            array('scc_sort_order' => ''));
        //Steps
        $this->systemConfigurationHelper()->configure($paymentMethod);
        $paymentMethod = $this->loadDataSet('PaymentMethod', 'savedcc_without_3Dsecure',
            array('scc_sort_order' => rand(1, 10)));
        $this->systemConfigurationHelper()->configure($paymentMethod);
    }
}