<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Model_System_Config_Source_Admin_Page
 */

class Mage_Adminhtml_Model_System_Config_Source_Admin_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected $_menuModel;

    /**
     * @var Mage_Backend_Model_Menu
     */
    protected $_menuSubModel;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var Mage_Adminhtml_Model_System_Config_Source_Admin_Page
     */
    protected $_model;

    public function setUp()
    {
        $logger = $this->getMock('Mage_Backend_Model_Menu_Logger');
        $this->_menuModel = new Mage_Backend_Model_Menu(array('logger' => $logger));
        $this->_menuSubModel = new Mage_Backend_Model_Menu(array('logger' => $logger));

        $this->_factoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $item1 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item1->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $item1->expects($this->any())->method('getTitle')->will($this->returnValue('Item 1'));
        $item1->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item1->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item1->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/item1'));
        $item1->expects($this->any())->method('getChildren')->will($this->returnValue($this->_menuSubModel));
        $item1->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $this->_menuModel->add($item1);

        $item2 = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $item2->expects($this->any())->method('getId')->will($this->returnValue('item2'));
        $item2->expects($this->any())->method('getTitle')->will($this->returnValue('Item 2'));
        $item2->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $item2->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $item2->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/item2'));
        $item2->expects($this->any())->method('hasChildren')->will($this->returnValue(false));
        $this->_menuSubModel->add($item2);

        $this->_model = new Mage_Adminhtml_Model_System_Config_Source_Admin_Page(
            array(
                'menu' => $this->_menuModel,
                'objectFactory' => $this->_factoryMock,
            )
        );
    }

    public function testToOptionArray()
    {
        $this->_factoryMock
            ->expects($this->at(0))
            ->method('getModelInstance')
            ->with(
            $this->equalTo('Mage_Backend_Model_Menu_Filter_Iterator'),
            $this->equalTo($this->_menuModel->getIterator())
            )->will($this->returnValue(new Mage_Backend_Model_Menu_Filter_Iterator($this->_menuModel->getIterator())));

        $this->_factoryMock
            ->expects($this->at(1))
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Filter_Iterator'),
                $this->equalTo($this->_menuSubModel->getIterator())
            )->will($this->returnValue(
                new Mage_Backend_Model_Menu_Filter_Iterator($this->_menuSubModel->getIterator())
            )
        );

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        $paddingString = str_repeat($nonEscapableNbspChar, 4);

        $expected = array(
            array(
                'label' => 'Item 1',
                'value' => 'item1',
            ),
            array(
                'label' => $paddingString . 'Item 2',
                'value' => 'item2',
            ),
        );
        $this->assertEquals($expected, $this->_model->toOptionArray());
    }
}