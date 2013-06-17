<?php
/**
 * {license_notice}
 *
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

    public function testIsAddProductRestricted()
    {
        $expected = true;
        $context = $this->getMock('Mage_Backend_Block_Template_Context', array(), array(), '', false);
        $limitation = $this->getMockForAbstractClass('Saas_Limitation_Model_Limitation_LimitationInterface');
        $limitationValidator = $this->getMock(
            'Saas_Limitation_Model_Limitation_Validator', array('exceedsThreshold')
        );
        $limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($limitation)
            ->will($this->returnValue($expected))
        ;
        $block = new Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile(
            $context, $limitationValidator, $limitation
        );
        $this->assertSame($expected, $block->isAddProductRestricted());
    }
}
