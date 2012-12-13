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
 * @magentoDataFixture Mage/Launcher/_files/config_bootstrap.php
 */
class Mage_Launcher_Helper_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Helper_Tile
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Mage_Launcher_Helper_Tile');
    }

    public function testGetStateResolverClassNameByTileCode()
    {
        $className = $this->_helper->getStateResolverClassNameByTileCode('tile_1');
        $this->assertEquals('Mage_Launcher_Model_Tile_StateResolverStub', $className);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage State Resolver is not defined for tile with code "tile_100".
     */
    public function testGetStateResolverClassNameByTileCodeThrowsExceptionWhenClassNameIsNotDefined()
    {
        // tile_100 configuration has not been defined by fixture
        $this->_helper->getStateResolverClassNameByTileCode('tile_100');
    }

    public function testGetSaveHandlerClassNameByTileCode()
    {
        $className = $this->_helper->getSaveHandlerClassNameByTileCode('tile_1');
        $this->assertEquals('Mage_Launcher_Model_Tile_SaveHandlerStub', $className);
    }

    /**
     * @expectedException Mage_Launcher_Exception
     * @expectedExceptionMessage Save Handler is not defined for tile with code "tile_100".
     */
    public function testGetSaveHandlerClassNameByTileCodeThrowsExceptionWhenClassNameIsNotDefined()
    {
        // tile_100 configuration has not been defined by fixture
        $this->_helper->getSaveHandlerClassNameByTileCode('tile_100');
    }
}
