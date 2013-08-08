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

class Saas_Launcher_Model_TileFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * This test covers the case of creation an empty Tile model without Save handler and State resolver.
     *
     * @covers Saas_Launcher_Model_TileFactory::create
     */
    public function testCreateEmptyTile()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);

        $tile = $this->getMock('Saas_Launcher_Model_Tile', array(), array(), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Saas_Launcher_Model_Tile'),
                $this->equalTo(array())
            )
            ->will($this->returnValue($tile));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManager, $applicationConfig);
        $this->assertSame($tile, $tileFactory->create());
    }

    /**
     * This test covers the case of creation Tile model with Save handler and State resolver.
     *
     * @covers Saas_Launcher_Model_TileFactory::create
     */
    public function testCreate()
    {
        $objectManager = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);

        $tile = $this->getMock(
            'Saas_Launcher_Model_Tile',
            array('loadByTileCode', 'getId', 'getPageCode', 'getTileCode'),
            array(),
            '',
            false
        );

        $tile->expects($this->once())
            ->method('loadByTileCode')
            ->with($this->equalTo('tile1'))
            ->will($this->returnValue($tile));

        $tile->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $tile->expects($this->once())
            ->method('getPageCode')
            ->will($this->returnValue('page1'));

        $tile->expects($this->once())
            ->method('getTileCode')
            ->will($this->returnValue('tile1'));

        $applicationConfig->expects($this->at(0))
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/page1/tiles/tile1/state_resolver'))
            ->will($this->returnValue('Saas_Launcher_Model_Tile_StateResolver'));

        $applicationConfig->expects($this->at(1))
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/page1/tiles/tile1/save_handler'))
            ->will($this->returnValue('Saas_Launcher_Model_Tile_SaveHandler'));

        $stateResolverStub = $this->getMock('Saas_Launcher_Model_Tile_StateResolver', array(), array(), '', false);
        $saveHandlerStub = $this->getMock('Saas_Launcher_Model_Tile_SaveHandler', array(), array(), '', false);

        $objectManager->expects($this->at(0))
            ->method('create')
            ->with(
                $this->equalTo('Saas_Launcher_Model_Tile'),
                $this->equalTo(array())
            )
            ->will($this->returnValue($tile));

        $objectManager->expects($this->at(1))
            ->method('create')
            ->with(
                $this->equalTo('Saas_Launcher_Model_Tile_StateResolver'),
                $this->equalTo(array())
            )
            ->will($this->returnValue($stateResolverStub));

        $objectManager->expects($this->at(2))
            ->method('create')
            ->with(
                $this->equalTo('Saas_Launcher_Model_Tile_SaveHandler'),
                $this->equalTo(array())
            )
            ->will($this->returnValue($saveHandlerStub));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManager, $applicationConfig);
        $this->assertSame($tile, $tileFactory->create('tile1'));
        $this->assertSame($stateResolverStub, $tile->getStateResolver());
        $this->assertSame($saveHandlerStub, $tile->getSaveHandler());
    }

    public function testGetStateResolverClassName()
    {
        $objectManagerStub = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);

        $applicationConfig->expects($this->once())
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/pageCode/tiles/tileCode/state_resolver'))
            ->will($this->returnValue('Saas_Launcher_Model_Tile_StateResolver'));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManagerStub, $applicationConfig);
        $result = $tileFactory->getStateResolverClassName('pageCode', 'tileCode');
        $this->assertEquals('Saas_Launcher_Model_Tile_StateResolver', $result);
    }

    /**
     * @covers Saas_Launcher_Model_TileFactory::getStateResolverClassName
     * @expectedException Saas_Launcher_Exception
     * @expectedExceptionMessage State Resolver is not defined for tile with code "tileCode".
     */
    public function testGetStateResolverClassNameThrowsException()
    {
        $objectManagerStub = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);

        $applicationConfig->expects($this->once())
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/pageCode/tiles/tileCode/state_resolver'))
            ->will($this->returnValue(''));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManagerStub, $applicationConfig);
        $tileFactory->getStateResolverClassName('pageCode', 'tileCode');
    }

    public function testGetSaveHandlerClassName()
    {
        $objectManagerStub = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);

        $applicationConfig->expects($this->once())
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/pageCode/tiles/tileCode/save_handler'))
            ->will($this->returnValue('Saas_Launcher_Model_Tile_StateResolver'));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManagerStub, $applicationConfig);
        $result = $tileFactory->getSaveHandlerClassName('pageCode', 'tileCode');
        $this->assertEquals('Saas_Launcher_Model_Tile_StateResolver', $result);
    }

    /**
     * @covers Saas_Launcher_Model_TileFactory::getSaveHandlerClassNameByTileCode
     * @expectedException Saas_Launcher_Exception
     * @expectedExceptionMessage Save Handler is not defined for tile with code "tileCode".
     */
    public function testGetSaveHandlerClassNameThrowsException()
    {
        $objectManagerStub = $this->getMock('Magento_ObjectManager');
        $applicationConfig = $this->getMock('Magento_Core_Model_Config', array('getNode'), array(), '', false);

        $applicationConfig->expects($this->once())
            ->method('getNode')
            ->with($this->equalTo('adminhtml/launcher/pages/pageCode/tiles/tileCode/save_handler'))
            ->will($this->returnValue(''));

        $tileFactory = new Saas_Launcher_Model_TileFactory($objectManagerStub, $applicationConfig);
        $tileFactory->getSaveHandlerClassName('pageCode', 'tileCode');
    }
}
