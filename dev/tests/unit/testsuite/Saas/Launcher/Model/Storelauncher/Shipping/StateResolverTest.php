<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_Storelauncher_Shipping_StateResolverTest
    extends Saas_Launcher_Model_Tile_ConfigBased_StateResolverTestCaseAbstract
{
    /**
     * @param Magento_Core_Model_App $app
     * @return Saas_Launcher_Model_Storelauncher_Shipping_StateResolver
     */
    protected function _getStateResolverInstance(Magento_Core_Model_App $app)
    {
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getPost')
            ->with($this->equalTo('shipping_enabled'), $this->equalTo(null))
            ->will($this->returnValue('1'));
        return new Saas_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $request);
    }

    public function testIsTileCompleteWhenShippingEnabledCheckboxIsNotChecked()
    {
        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->any())->method('getPost')
            ->with($this->equalTo('shipping_enabled'), $this->equalTo(null))
            ->will($this->returnValue('0'));
        $stateResolver = new Saas_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $request);
        $this->assertEquals(true, $stateResolver->isTileComplete());
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function isTileCompleteDataProvider()
    {
        // Shipping tile is considered to be complete when at least one of the related shipping methods is active
        return array(
            array(
                array(
                    'carriers/flatrate/active' => 1,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 1,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 1,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 1,
                    'carriers/dhlint/active' => 0,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 1,
                ),
                true,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                false,
            ),
            array(
                array(
                    'carriers/flatrate/active' => 1,
                    'carriers/ups/active' => 1,
                    'carriers/usps/active' => 1,
                    'carriers/fedex/active' => 1,
                    'carriers/dhlint/active' => 1,
                ),
                true,
            ),
        );
    }

    /**
     * Get Shipping State Resolver Mock object
     *
     * @param array $configSettings
     * @return Saas_Launcher_Model_Storelauncher_Shipping_StateResolver
     */
    protected function _getShippingStateResolverForConfiguredMethodsTest(array $configSettings)
    {
        $store = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);

        $store->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(
            function ($configPath) use ($configSettings) {
                return isset($configSettings[$configPath]) ? $configSettings[$configPath] : null;
            }
        ));

        $app = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $app->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));
        $request = $this->getMock('Magento_Core_Controller_Request_Http', array(), array(), '', false);
        $stateResolver = new Saas_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $request);
        return $stateResolver;
    }

    /**
     * @param array $configSettings
     * @param boolean $expectedResult
     * @dataProvider relatedShippingMethodDataProvider
     */
    public function testIsShippingConfigured($configSettings, $expectedResult)
    {
        $stateResolver = $this->_getShippingStateResolverForConfiguredMethodsTest($configSettings);
        $this->assertEquals($expectedResult, $stateResolver->isShippingConfigured());
    }

    public function relatedShippingMethodDataProvider()
    {
        return array(
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 1,
                    'carriers/fedex/active' => 1,
                    'carriers/dhlint/active' => 0,
                ),
                true
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/ups/active' => 0,
                    'carriers/usps/active' => 0,
                    'carriers/fedex/active' => 0,
                    'carriers/dhlint/active' => 0,
                ),
                false
            )
        );
    }

    /**
     * @param array $configSettings
     * @param boolean $expectedResult
     * @dataProvider getConfiguredShippingMethods
     */
    public function testGetConfiguredShippingMethods($configSettings, $expectedResult)
    {
        $stateResolver = $this->_getShippingStateResolverForConfiguredMethodsTest($configSettings);
        $this->assertEquals($expectedResult, $stateResolver->getConfiguredShippingMethods());
    }

    public function getConfiguredShippingMethods()
    {
        return array(
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/flatrate/title' => 'Flatrate Title',
                    'carriers/ups/active' => 0,
                    'carriers/ups/title' => 'UPS Title',
                    'carriers/usps/active' => 1,
                    'carriers/usps/title' => 'USPS Title',
                    'carriers/fedex/active' => 1,
                    'carriers/fedex/title' => 'Fedex Title',
                    'carriers/dhlint/active' => 0,
                    'carriers/dhlint/title' => 'DHL Title',
                ),
                array(
                    'usps' => 'USPS Title',
                    'fedex' => 'Fedex Title'
                )
            ),
            array(
                array(
                    'carriers/flatrate/active' => 0,
                    'carriers/flatrate/title' => 'Flatrate Title',
                    'carriers/ups/active' => 0,
                    'carriers/ups/title' => 'UPS Title',
                    'carriers/usps/active' => 0,
                    'carriers/usps/title' => 'USPS Title',
                    'carriers/fedex/active' => 0,
                    'carriers/fedex/title' => 'Fedex Title',
                    'carriers/dhlint/active' => 0,
                    'carriers/dhlint/title' => 'DHL Title',
                ),
                array()
            )
        );
    }
}
