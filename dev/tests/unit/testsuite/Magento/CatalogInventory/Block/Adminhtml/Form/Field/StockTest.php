<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogInventory_Block_Adminhtml_Form_Field_StockTest extends PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var Magento_Data_Form_Element_Text|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_qty;

    /**
     * @var Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock
     */
    protected $_block;

    /**
     * @var Magento_Data_Form_Element_TextFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_textFactoryMock;

    protected function setUp()
    {
        $this->_qty = $this->getMock('Magento_Data_Form_Element_Text',
            array('getElementHtml', 'setForm', 'setValue', 'setName', 'addClass')
        );

        $this->_textFactoryMock = $this->getMock('Magento_Data_Form_Element_TextFactory', array('create'));
        $this->_textFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_qty));

        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock',
            array(
                'textFactory' => $this->_textFactoryMock,
                'attributes' => array(
                    'qty' => $this->_qty,
                    'name' => 'name',
                ),
            )
        );
    }

    public function testSetForm()
    {
        $this->_qty->expects($this->once())->method('setForm')
            ->with($this->isInstanceOf('Magento_Data_Form_Element_Abstract'));
        $this->_block->setForm(new Magento_Data_Form_Element_Text());
    }

    public function testSetValue()
    {
        $value = array('qty' => 1, 'is_in_stock' => 0);
        $this->_qty->expects($this->once())->method('setValue')->with($this->equalTo(1));
        $this->_block->setValue($value);
    }

    public function testSetName()
    {
        $this->_qty->expects($this->once())->method('setName')->with(self::ATTRIBUTE_NAME . '[qty]')
            ->will($this->returnValue($this->_qty));

        $this->_block->setName(self::ATTRIBUTE_NAME);
    }
}
