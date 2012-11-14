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
 */
class Mage_Launcher_Model_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Tile
     */
    protected $_tile;

    protected function setUp()
    {
        $this->_tile = Mage::getModel('Mage_Launcher_Model_Tile');
    }

    public function testLoadByCode()
    {
        $this->_tile->loadByCode('tile_1');
        $this->assertEquals('tile_1', $this->_tile->getCode());
    }
}
