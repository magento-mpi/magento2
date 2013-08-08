<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Model_TileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Launcher_Model_Tile
     */
    protected $_tile;

    public function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $resource = $this->getMock('Saas_Launcher_Model_Resource_Tile', array(), array(), '', false);
        $resource->expects($this->any())
            ->method('addCommitCallback')
            ->will($this->returnValue($resource));

        $this->_tile = new Saas_Launcher_Model_Tile(
            $helper->getObject('Magento_Core_Model_Context'),
            $resource,
            null,
            null,
            null,
            array()
        );
    }

    public function testIsComplete()
    {
        $this->assertFalse($this->_tile->isComplete());
        $this->_tile->setState(Saas_Launcher_Model_Tile::STATE_COMPLETE);
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
        $mockSaveHandler = $this->getMock('Saas_Launcher_Model_Tile_SaveHandler');
        if (!empty($data)) {
            $mockSaveHandler->expects($this->once())
                ->method('save')
                ->with($this->equalTo($data));
        }
        $this->_tile->setSaveHandler($mockSaveHandler);
        $mockStateResolver = $this->getMock('Saas_Launcher_Model_Tile_StateResolver',
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
                Saas_Launcher_Model_Tile::STATE_TODO,
                Saas_Launcher_Model_Tile::STATE_TODO
            ),
            array(
                array(),
                Saas_Launcher_Model_Tile::STATE_COMPLETE,
                Saas_Launcher_Model_Tile::STATE_COMPLETE
            ),
            array(
                array('param' => 1),
                Saas_Launcher_Model_Tile::STATE_SKIPPED,
                Saas_Launcher_Model_Tile::STATE_SKIPPED
            ),
            array(
                array('param' => 1),
                Saas_Launcher_Model_Tile::STATE_DISMISSED,
                Saas_Launcher_Model_Tile::STATE_DISMISSED
            ),
        );
    }
}
