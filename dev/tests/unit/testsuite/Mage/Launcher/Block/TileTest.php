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
 * Test class for Mage_Launcher_Block_Tile
 */
class Mage_Launcher_Block_TileTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Launcher_Block_Tile */
    protected $block;
    /** @var Mage_Launcher_Model_Tile */
    protected $tile;


    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);

        $arguments = array(
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false)
        );
        $this->block = $objectManager->getBlock('Mage_Launcher_Block_Tile', $arguments);

        $this->tile = $objectManager->getModel('Mage_Launcher_Model_Tile');
        $this->block->setTile($this->tile);
    }

    protected function tearDown()
    {
        $this->block = null;
        $this->tile = null;
    }

    /**
     * @covers Mage_Launcher_Block_Tile::getTileCode
     */
    public function testGetTileCode()
    {
        $tileCode = 'tax';
        $this->tile->setCode($tileCode);

        $this->assertEquals($tileCode, $this->block->getTileCode());
    }

    /**
     * @covers Mage_Launcher_Block_Tile::getTileCode
     */
    public function testGetTileState()
    {
        $tileState = 1;
        $this->tile->setState($tileState);

        $this->assertEquals($tileState, $this->block->getTileState());
    }
}
