<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Launcher/_files/pages.php
 * @magentoDataFixture Mage/Launcher/_files/config_bootstrap.php
 */
class Mage_Launcher_Model_TileFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_TileFactory
     */
    protected $_tileFactory;

    protected function setUp()
    {
        $this->_tileFactory = Mage::getModel('Mage_Launcher_Model_TileFactory');
    }

    /**
     * @dataProvider createDataProvider
     * @param string $tileCode
     * @param boolean $isEmpty
     */
    public function testCreate($tileCode, $isEmpty)
    {
        $tile = $this->_tileFactory->create($tileCode);
        $this->assertInstanceOf('Mage_Launcher_Model_Tile', $tile);
        if ($isEmpty) {
            $this->assertNull($tile->getStateResolver());
            $this->assertNull($tile->getSaveHandler());
        } else {
            $this->assertInstanceOf('Mage_Launcher_Model_Tile_StateResolverStub', $tile->getStateResolver());
            $this->assertInstanceOf('Mage_Launcher_Model_Tile_SaveHandlerStub', $tile->getSaveHandler());
        }
    }

    public function createDataProvider()
    {
        return array(
            array(
                'tile_1',
                false
            ),
            array(
                null,
                true
            )
        );
    }

    /**
     * @covers Mage_Launcher_Model_TileFactory::create
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Tile is not defined for specified tile code: "tile_100".
     * @param string $tileCode
     */
    public function testCreateThrowsException()
    {
        $this->_tileFactory->create('tile_100');
    }

    public function testGetStateResolverClassName()
    {
        $className = $this->_tileFactory->getStateResolverClassName('landing_page_1', 'tile_1');
        $this->assertEquals('Mage_Launcher_Model_Tile_StateResolverStub', $className);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage State Resolver is not defined for tile with code "tile_100".
     */
    public function testGetStateResolverClassNameThrowsExceptionWhenClassNameIsNotDefined()
    {
        // tile_100 configuration has not been defined by fixture
        $this->_tileFactory->getStateResolverClassName('landing_page_1', 'tile_100');
    }

    public function testGetSaveHandlerClassName()
    {
        $className = $this->_tileFactory->getSaveHandlerClassName('landing_page_1', 'tile_1');
        $this->assertEquals('Mage_Launcher_Model_Tile_SaveHandlerStub', $className);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Save Handler is not defined for tile with code "tile_100".
     */
    public function testGetSaveHandlerClassNameThrowsExceptionWhenClassNameIsNotDefined()
    {
        // tile_100 configuration has not been defined by fixture
        $this->_tileFactory->getSaveHandlerClassName('landing_page_1', 'tile_100');
    }
}
