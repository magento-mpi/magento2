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
     * @var Magento_Core_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var Magento_Data_Form_Element_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryElementMock;

    /**
     * @var Magento_Data_Form_Element_CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactoryMock;
    
    /**
     * @var Magento_Data_Form_Element_Text|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_qtyMock;

    /**
     * @var Magento_Data_Form_Element_TextFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryTextMock;
    
    /**
     * @var Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock
     */
    protected $_block;

    protected function setUp()
    {
        $this->_coreHelperMock = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);        
        $this->_factoryElementMock = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $this->_collectionFactoryMock = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array(),
            array(), '', false);
        $this->_qtyMock = $this->getMock('Magento_Data_Form_Element_Text', array('setForm', 'setValue', 'setName'),
            array(), '', false);
        $this->_factoryTextMock = $this->getMock('Magento_Data_Form_Element_TextFactory', array('create'));

        $objectManagerHelper = new \Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject('Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock',
            array(
                'coreData' => $this->_coreHelperMock,
                'factoryElement' => $this->_factoryElementMock,
                'factoryCollection' => $this->_collectionFactoryMock,
                'factoryText' => $this->_factoryTextMock,
                'attributes' => array(
                    'qty' => $this->_qtyMock,
                    'name' => self::ATTRIBUTE_NAME,
                ),
            )
        );
    }
    
    public function testSetForm()
    {
        $this->_qtyMock->expects($this->once())->method('setForm')
            ->with($this->isInstanceOf('Magento_Data_Form_Element_Abstract'));

        $this->_block->setForm(new Magento_Data_Form_Element_Text(
            $this->_coreHelperMock, 
            $this->_factoryElementMock, 
            $this->_collectionFactoryMock
        ));
    }

    public function testSetValue()
    {
        $value = array('qty' => 1, 'is_in_stock' => 0);
        $this->_qtyMock->expects($this->once())->method('setValue')->with($this->equalTo(1));

        $this->_block->setValue($value);
    }

    public function testSetName()
    {
        $this->_qtyMock->expects($this->once())->method('setName')->with(self::ATTRIBUTE_NAME . '[qty]');

        $this->_block->setName(self::ATTRIBUTE_NAME);
    }
}
