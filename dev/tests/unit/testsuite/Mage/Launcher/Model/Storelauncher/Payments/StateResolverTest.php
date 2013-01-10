<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_Storelauncher_Payments_StateResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider handleSystemConfigChangeDataProvider
     * @param int $currentState
     */
    public function testHandleSystemConfigChange($currentState)
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $stateResolver = new Mage_Launcher_Model_Storelauncher_Payments_StateResolver($app, $config);
        // Payments tile is not system-config depended, so this method always has to return current tile state
        $resultState = $stateResolver->handleSystemConfigChange('general', $currentState);
        $this->assertEquals($currentState, $resultState);
    }

    public function handleSystemConfigChangeDataProvider()
    {
        return array(
            array(Mage_Launcher_Model_Tile::STATE_COMPLETE),
            array(Mage_Launcher_Model_Tile::STATE_TODO),
        );
    }

    /**
     * @covers Mage_Launcher_Model_Storelauncher_Payments_StateResolver::isTileComplete
     * @param array $paymentSettings
     * @param boolean $expectedResult
     * @dataProvider isTileCompleteDataProvider
     */
    public function testIsTileComplete(array $paymentSettings, $expectedResult)
    {
        $stateResolver = $this->_getStateResolverForIsTileCompleteTest($paymentSettings);
        $this->assertEquals(
            $expectedResult,
            $stateResolver->isTileComplete()
        );
    }

    /**
     * @covers Mage_Launcher_Model_Storelauncher_Payments_StateResolver::getPersistentState
     * @param array $paymentSettings
     * @param boolean $isTileComplete
     * @dataProvider isTileCompleteDataProvider
     */
    public function testGetPersistentState(array $paymentSettings, $isTileComplete)
    {
        $stateResolver = $this->_getStateResolverForIsTileCompleteTest($paymentSettings);
        $expectedResult = ($isTileComplete)
            ? Mage_Launcher_Model_Tile::STATE_COMPLETE
            : Mage_Launcher_Model_Tile::STATE_TODO;
        $this->assertEquals(
            $expectedResult,
            $stateResolver->getPersistentState()
        );
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function isTileCompleteDataProvider()
    {
        // Payments tile is considered to be complete when at least one of the related payment methods is active
        return array(
            array(
                array(
                    'payment/paypal_express/active' => 1,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 1,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 1,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 1,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 1,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 1,
                    'payment/authorizenet/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 1,
                ),
                true,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 0,
                    'payment/paypal_standard/active' => 0,
                    'payment/payflow_advanced/active' => 0,
                    'payment/paypal_direct/active' => 0,
                    'payment/payflow_link/active' => 0,
                    'payment/verisign/active' => 0,
                    'payment/authorizenet/active' => 0,
                ),
                false,
            ),
            array(
                array(
                    'payment/paypal_express/active' => 1,
                    'payment/paypal_standard/active' => 1,
                    'payment/payflow_advanced/active' => 1,
                    'payment/paypal_direct/active' => 1,
                    'payment/payflow_link/active' => 1,
                    'payment/verisign/active' => 1,
                    'payment/authorizenet/active' => 1,
                ),
                true,
            ),
        );
    }

    /**
     * Retrieve State Resolver instance for isTileComplete test
     *
     * @param array $paymentSettings
     * @return Mage_Launcher_Model_Storelauncher_Payments_StateResolver
     */
    protected function _getStateResolverForIsTileCompleteTest(array $paymentSettings)
    {
        $store = $this->getMock('Mage_Core_Model_Store', array('getConfig'), array(), '', false);

        // Mock getConfig() call
        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(
                function ($configPath) use ($paymentSettings) {
                    return isset($paymentSettings[$configPath]) ? $paymentSettings[$configPath] : null;
                }
            ));

        // Create mock object of Application
        $app = $this->getMock('Mage_Core_Model_App', array('getStore'), array(), '', false);
        $app->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        // Create mock object of Configuration
        $config = $this->getMock('Mage_Core_Model_Config', array('reinit'), array(), '', false);
        $config->expects($this->once())
            ->method('reinit')
            ->will($this->returnValue($config));

        return new Mage_Launcher_Model_Storelauncher_Payments_StateResolver($app, $config);
    }
}
