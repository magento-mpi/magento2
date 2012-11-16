<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Launcher_Model_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Model_Tile
     */
    protected $_tile;

    public function setUp()
    {
        $eventManager = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $cacheManager = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $resource = $this->getMock('Mage_Launcher_Model_Resource_Tile', array(), array(), '', false);
        $this->_tile = new Mage_Launcher_Model_Tile($eventManager, $cacheManager, $resource, null, array());
    }

    public function testIsComplete()
    {
        $this->assertFalse($this->_tile->isComplete());
        $this->_tile->setState(Mage_Launcher_Model_Tile::STATE_COMPLETE);
        $this->assertTrue($this->_tile->isComplete());
    }

    public function testIsSkippable()
    {
        $this->assertFalse($this->_tile->isSkippable());
        $this->_tile->setIsSkippable(true);
        $this->assertTrue($this->_tile->isSkippable());
    }

    public function testIsDismissible()
    {
        $this->assertFalse($this->_tile->isDismissible());
        $this->_tile->setIsDismissible(true);
        $this->assertTrue($this->_tile->isDismissible());
    }
}
