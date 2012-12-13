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
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnValue($resource));
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

    /**
     * @dataProvider generateRefreshStateData
     * @param array $data
     * @param int $setState
     * @param int $expectedState
     */
    public function testRefreshState($data, $setState, $expectedState)
    {
        $mockSaveHandler = $this->getMock('Mage_Launcher_Model_Tile_SaveHandler');
        if (!empty($data)) {
            $mockSaveHandler->expects($this->once())
                ->method('save')
                ->with($this->equalTo($data));
        }
        $this->_tile->setSaveHandler($mockSaveHandler);
        $mockStateResolver = $this->getMock('Mage_Launcher_Model_Tile_StateResolver',
            array('getPersistentState', 'handleSystemConfigChange', 'isTileComplete'),
            array(),
            '',
            false
        );

        $mockStateResolver->expects($this->once())
            ->method('getPersistentState')
            ->will($this->returnValue($setState));

        $this->_tile->setStateResolver($mockStateResolver);

        $this->_tile->refreshState($data);
        $this->assertEquals($expectedState, $this->_tile->getState());
    }

    /**
     * Data provider for testRefreshState method
     *
     * @return array
     */
    public function generateRefreshStateData()
    {
        return array(
            array(
                array(),
                Mage_Launcher_Model_Tile::STATE_TODO,
                Mage_Launcher_Model_Tile::STATE_TODO
            ),
            array(
                array(),
                Mage_Launcher_Model_Tile::STATE_COMPLETE,
                Mage_Launcher_Model_Tile::STATE_COMPLETE
            ),
            array(
                array('param' => 1),
                Mage_Launcher_Model_Tile::STATE_SKIPPED,
                Mage_Launcher_Model_Tile::STATE_SKIPPED
            ),
            array(
                array('param' => 1),
                Mage_Launcher_Model_Tile::STATE_DISMISSED,
                Mage_Launcher_Model_Tile::STATE_DISMISSED
            ),
        );
    }
}
