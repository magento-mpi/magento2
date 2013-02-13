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

class Mage_Launcher_Model_Storelauncher_Shipping_StateResolverTest
    extends Mage_Launcher_Model_Tile_ConfigBased_StateResolverTestCaseAbstract
{
    /**
     * @param Mage_Core_Model_App $app
     * @return Mage_Launcher_Model_Storelauncher_Shipping_StateResolver
     */
    protected function _getStateResolverInstance(Mage_Core_Model_App $app)
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->any())
            ->method('getPost')
            ->with($this->equalTo('shipping_enabled'), $this->equalTo(null))
            ->will($this->returnValue('1'));
        return new Mage_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $request);
    }

    public function testIsTileCompleteWhenShippingEnabledCheckboxIsNotChecked()
    {
        $app = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $request->expects($this->any())->method('getPost')
            ->with($this->equalTo('shipping_enabled'), $this->equalTo(null))
            ->will($this->returnValue('0'));
        $stateResolver = new Mage_Launcher_Model_Storelauncher_Shipping_StateResolver($app, $request);
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
}
