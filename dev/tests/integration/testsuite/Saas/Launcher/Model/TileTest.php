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
 * @magentoDataFixture Saas/Launcher/_files/pages.php
 * @magentoDataFixture Saas/Launcher/_files/config_bootstrap.php
 */
class Saas_Launcher_Model_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Tile
     */
    protected $_tile;

    protected function setUp()
    {
        $this->_tile = Mage::getModel('Saas_Launcher_Model_TileFactory')->create();
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
        $tile = Mage::getModel('Saas_Launcher_Model_Tile');
        $tile->setTileCode('tile_1')
            ->save();
    }
}
