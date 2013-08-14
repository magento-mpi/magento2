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

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_DrawerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isShippingEnabledDataProvider
     * @param boolean $tileState
     * @param boolean $isShippingConfigured
     * @param boolean $expectedResult
     */
    public function testIsShippingEnabled($tileState, $isShippingConfigured, $expectedResult)
    {
        $shippingDrawerBlock = $this->_getShippingDrawerBlockMock($tileState, $isShippingConfigured);
        $this->assertInstanceOf('Saas_Launcher_Model_Tile', $shippingDrawerBlock->getTile());
        $this->assertEquals($expectedResult, $shippingDrawerBlock->isShippingEnabled());
    }

    /**
     * Get Mock for Shipping Drawer Block
     *
     * @param boolean $tileState
     * @param boolean $isShippingConfigured
     * @return Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer
     */
    protected function _getShippingDrawerBlockMock($tileState, $isShippingConfigured)
    {
        $tileModel = $this->getMock(
            'Saas_Launcher_Model_Tile',
            array('getState', 'getStateResolver'),
            array(),
            '',
            false
        );

        $tileModel->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($tileState));

        $stateResolver = $this->getMock(
            'Saas_Launcher_Model_Storelauncher_Shipping_StateResolver',
            array(),
            array(),
            '',
            false
        );

        $stateResolver->expects($this->any())
            ->method('isShippingConfigured')
            ->will($this->returnValue($isShippingConfigured));

        $tileModel->expects($this->any())
            ->method('getStateResolver')
            ->will($this->returnValue($stateResolver));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $shippingDrawerBlock = $objectManagerHelper->getObject(
            'Saas_Launcher_Block_Adminhtml_Storelauncher_Shipping_Drawer'
        );
        $shippingDrawerBlock->setTile($tileModel);
        return $shippingDrawerBlock;
    }

    /**
     * Data Provider for testIsShippingEnabled() method
     */
    public function isShippingEnabledDataProvider()
    {
        return array(
            array(
                Saas_Launcher_Model_Tile::STATE_COMPLETE,
                true,
                true
            ),
            array(
                Saas_Launcher_Model_Tile::STATE_COMPLETE,
                false,
                false
            ),
            array(
                Saas_Launcher_Model_Tile::STATE_TODO,
                false,
                true
            ),
            array(
                Saas_Launcher_Model_Tile::STATE_TODO,
                true,
                true
            ),
        );
    }
}
