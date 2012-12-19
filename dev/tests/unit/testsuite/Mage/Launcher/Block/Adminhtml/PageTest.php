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
class Mage_Launcher_Block_Adminhtml_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Launcher_Block_Adminhtml_Page
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $arguments = array(
            'urlBuilder' => $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false),
            'pageFactory' => $this->getMock('Mage_Launcher_Model_PageFactory', array(), array(), '', false)
        );
        $this->_block = $objectManager->getBlock('Mage_Launcher_Block_Adminhtml_Page', $arguments);
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @dataProvider getIsAllStepsCompletedData
     * @covers Mage_Launcher_Block_Adminhtml_Page::isAllStepsCompleted
     */
    public function testIsAllStepsCompleted($isAllStepsCompleted, $tilesData)
    {
        $page = $this->getMock('Mage_Launcher_Model_Page', array('getTiles'), array(), '', false);

        $page->expects($this->once())
            ->method('getTiles')
            ->will($this->returnValue($this->_getMockedTiles($tilesData)));

        $this->_block->setPage($page);

        $this->assertEquals($isAllStepsCompleted, $this->_block->isAllStepsCompleted());
    }

    /**
     * Get provider data
     * @return array
     */
    public function getIsAllStepsCompletedData()
    {
        return array(
            array(true, array(true, true, true, true)),
            array(false, array(true, false, true, false))
        );
    }

    /**
     * Get list of mocked tiles
     *
     * @param array $tilesData
     * @return array
     */
    protected function _getMockedTiles($tilesData)
    {
        $tilesList = array();
        foreach($tilesData as $isTileCompleted) {
            $tile = $this->getMock('Mage_Launcher_Model_Tile', array('isComplete'), array(), '', false);

            $tile->expects($this->any())
                ->method('isComplete')
                ->will($this->returnValue($isTileCompleted));

            $tilesList[] = $tile;
        }
        return $tilesList;
    }
}
