<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test class Saas_Launcher_Model_Storelauncher_Shipping_SaveHandlerTest
 */
class Saas_Launcher_Model_Storelauncher_Shipping_SaveHandlerTest extends PHPUnit_Framework_TestCase
{
    /** @var array $_carriers Array of predefined carrierrs */
    protected $_activeCarriers = array(
        'dhl2',
        'fedex2',
        'ups2',
        'usps2',
        'dhlint2',
    );

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Saas/Launcher/_files/config_bootstrap.php
     */
    public function testSave()
    {
        $this->markTestIncomplete('Incorrect usage of magentoDataFixture');
        Mage::getConfig()->setCurrentAreaCode('adminhtml');
        /** @var Saas_Launcher_Model_Storelauncher_Shipping_SaveHandler */
        $shippingSaveHandler = Mage::getModel('Saas_Launcher_Model_Storelauncher_Shipping_SaveHandler');

        $shippingSaveHandler->save(array('shipping_enabled' => true));
        $activeCarries = $this->_getActiveShippingCarriers();
        foreach ($this->_activeCarriers as $carrier) {
            $this->assertContains($carrier, $activeCarries);
        }

        $shippingSaveHandler->save(array());
        $activeCarries = $this->_getActiveShippingCarriers();
        foreach ($this->_activeCarriers as $carrier) {
            $this->assertNotContains($carrier, $activeCarries);
        }
    }

    /**
     * Get list of active shipping carriers
     *
     * @return array
     */
    protected function _getActiveShippingCarriers()
    {
        $config = Mage::getModel('Magento_Core_Model_Config');
        $carriers = $config->getNode('default/carriers');
        $activeCarries = array();
        foreach ($carriers->children() as $carrierName => $carrier) {
            $currentCarrier = $carrier->asCanonicalArray();
            if ($currentCarrier['active'] == '1') {
                $activeCarries[] = $carrierName;
            }
        }
        return $activeCarries;
    }
}
