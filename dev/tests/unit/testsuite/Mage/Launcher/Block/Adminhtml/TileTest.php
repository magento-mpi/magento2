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

        $arguments = array(
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
        );
        $this->_block = $objectManager->getBlock('Mage_Launcher_Block_Adminhtml_Tile', $arguments);

        $this->_tile = $objectManager->getModel('Mage_Launcher_Model_Tile');
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
        $this->_tile->setCode($tileCode);

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
