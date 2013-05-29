<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Launcher_Block_Adminhtml_Storelauncher_Product_TileTest
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_TileTest extends PHPUnit_Framework_TestCase
{
    /** @var Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile */
    protected $_block;

    protected function setUp()
    {
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_block = $objectManager->getObject('Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    /**
     * @covers Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile::getTileState
     */
    public function testGetTileState()
    {
        $tile = $this->getMockBuilder('Saas_Launcher_Model_Tile')
            ->disableOriginalConstructor()
            ->setMethods(array('isComplete', 'refreshState', 'getState'))
            ->getMock();

        $tile->expects($this->any())->method('isComplete')
            ->will($this->returnValue(true));

        $tile->expects($this->once())->method('refreshState');

        $tile->expects($this->once())->method('getState')
            ->will($this->returnValue(Saas_Launcher_Model_Tile::STATE_TODO));

        $this->_block->setTile($tile);
        $this->assertEquals(Saas_Launcher_Model_Tile::STATE_TODO, $this->_block->getTileState());
    }
}
