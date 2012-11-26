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
class Mage_PageCache_Helper_TileTest extends PHPUnit_Framework_TestCase
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
     */
    public function testGetStateResolverClassNameByTileCodeThrowsExceptionWhenClassNameIsNotDefined()
    {
        // tile_100 configuration has not been defined by fixture
        $this->_helper->getStateResolverClassNameByTileCode('tile_100');
    }
}
