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
 * Test class for Mage_Backend_Model_Config_Source_Admin_Page
 */

class Mage_Backend_Model_Config_Source_Admin_PageTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_Backend_Model_Config_Source_Admin_Page
     */
    protected $_model;

    public function setUp()
    {
        $logger = $this->getMock('Mage_Core_Model_Logger', array(), array(), '', false);
        $this->_menuModel = new Mage_Backend_Model_Menu($logger);
        $this->_menuSubModel = new Mage_Backend_Model_Menu($logger);

        $this->_factoryMock = $this->getMock(
            'Mage_Backend_Model_Menu_Filter_IteratorFactory', array('create'), array(), '', false
        );

        $itemOne = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $itemOne->expects($this->any())->method('getId')->will($this->returnValue('item1'));
        $itemOne->expects($this->any())->method('getTitle')->will($this->returnValue('Item 1'));
        $itemOne->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $itemOne->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $itemOne->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/item1'));
        $itemOne->expects($this->any())->method('getChildren')->will($this->returnValue($this->_menuSubModel));
        $itemOne->expects($this->any())->method('hasChildren')->will($this->returnValue(true));
        $this->_menuModel->add($itemOne);

        $itemTwo = $this->getMock('Mage_Backend_Model_Menu_Item', array(), array(), '', false);
        $itemTwo->expects($this->any())->method('getId')->will($this->returnValue('item2'));
        $itemTwo->expects($this->any())->method('getTitle')->will($this->returnValue('Item 2'));
        $itemTwo->expects($this->any())->method('isAllowed')->will($this->returnValue(true));
        $itemTwo->expects($this->any())->method('isDisabled')->will($this->returnValue(false));
        $itemTwo->expects($this->any())->method('getAction')->will($this->returnValue('adminhtml/item2'));
        $itemTwo->expects($this->any())->method('hasChildren')->will($this->returnValue(false));
        $this->_menuSubModel->add($itemTwo);

        $menuConfig = $this->getMock('Mage_Backend_Model_Menu_Config', array(), array(), '', false);
        $menuConfig->expects($this->once())->method('getMenu')->will($this->returnValue($this->_menuModel));

        $this->_model = new Mage_Backend_Model_Config_Source_Admin_Page($this->_factoryMock, $menuConfig);
    }

    public function testToOptionArray()
    {
        $this->_factoryMock
            ->expects($this->at(0))
            ->method('create')
            ->with(
                $this->equalTo(array('iterator' => $this->_menuModel->getIterator()))
            )->will($this->returnValue(new Mage_Backend_Model_Menu_Filter_Iterator($this->_menuModel->getIterator())));

        $this->_factoryMock
            ->expects($this->at(1))
            ->method('create')
            ->with(
                $this->equalTo(array('iterator' => $this->_menuSubModel->getIterator()))
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
