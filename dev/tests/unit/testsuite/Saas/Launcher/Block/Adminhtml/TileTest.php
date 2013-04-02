<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Launcher_Block_Adminhtml_Tile
 */
class Mage_Launcher_Block_Adminhtml_TileTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Launcher_Block_Adminhtml_Tile */
    protected $_block;
    /** @var Mage_Launcher_Model_Tile */
    protected $_tile;


    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Mage_Launcher_Block_Adminhtml_Tile');
        $this->_tile = $objectManager->getObject('Mage_Launcher_Model_Tile');
        $this->_block->setTile($this->_tile);
    }

    protected function tearDown()
    {
        $this->_block = null;
        $this->_tile = null;
    }

    /**
     * @covers Mage_Launcher_Block_Adminhtml_Tile::getTileCode
     */
    public function testGetTileCode()
    {
        $tileCode = 'tax';
        $this->_tile->setTileCode($tileCode);

        $this->assertEquals($tileCode, $this->_block->getTileCode());
    }

    /**
     * @covers Mage_Launcher_Block_Adminhtml_Tile::getTileState
     */
    public function testGetTileState()
    {
        $tileState = 1;
        $this->_tile->setState($tileState);

        $this->assertEquals($tileState, $this->_block->getTileState());
    }
}
