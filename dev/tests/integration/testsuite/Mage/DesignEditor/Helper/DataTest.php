<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for skin changing observer
 *
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_Model_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new Mage_DesignEditor_Helper_Data();
    }

    /**
     * @param array $params
     * @param boolean $expectedResult
     *
     * @dataProvider isBlockDraggableDataProvider
     */
    public function testIsBlockDraggable($params, $expectedResult)
    {
        $block = $this->getMock('Mage_Core_Block_Template', array('getParentBlock'), array(), $params['block']);
        $parentBlock = $this->getMock($params['container'], array('getType'));

        $parentBlock->expects(self::any())
            ->method('getType')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return($params['container']));
        $block->expects(self::any())
            ->method('getParentBlock')
            ->will(new PHPUnit_Framework_MockObject_Stub_Return($parentBlock));

        $this->assertEquals($expectedResult, $this->_helper->isBlockDraggable($block));
    }

    public function isBlockDraggableDataProvider()
    {
        return array(
            'draggable_block' => array(
                'params' => array(
                    'block'  => 'Mage_Module_Block_Items_List',
                    'container' => 'Mage_Core_Block_Text_List'
                ),
                'expectedResult' => true
            ),
            'not_draggable_body' => array(
                'params' => array(
                    'block'  => 'Mage_Module_Block_Items_Info',
                    'container' => 'Mage_Core_Block_Text_List_Item'
                ),
                'expectedResult' => false
            ),
        );
    }
}
