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
class Mage_Launcher_Model_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Tile
     */
    protected $_tile;

    protected function setUp()
    {
        $this->_tile = Mage::getModel('Mage_Launcher_Model_TileFactory')->create();
    }

    public function testLoadByTileCode()
    {
        $this->_tile->loadByTileCode('tile_1');
        $this->assertEquals('tile_1', $this->_tile->getTileCode());
        $this->assertEquals('landing_page_1', $this->_tile->getPageCode());
    }

    public function testIsSkippableFlagSetByDatabaseByDefault()
    {
        // tile_1 was saved by fixture without specifying is_skippable property
        $this->assertFalse($this->_tile->isSkippable());
        $this->_tile->loadByTileCode('tile_1');
        $this->assertTrue($this->_tile->isSkippable());
    }

    public function testIsDismissibleFlagSetByDatabaseByDefault()
    {
        // tile_1 was saved by fixture without specifying is_dismissible property
        $this->assertFalse($this->_tile->isDismissible());
        $this->_tile->loadByTileCode('tile_1');
        $this->assertTrue($this->_tile->isDismissible());
    }

    /**
     * @expectedException Zend_Db_Statement_Exception
     */
    public function testSaveCannotPersistTwoTilesWithTheSameCode()
    {
        // tile tile_1 has been already created by fixture
        $tile = Mage::getModel('Mage_Launcher_Model_Tile');
        $tile->setTileCode('tile_1')
            ->save();
    }
}
