<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogInventory_Block_Adminhtml_Form_Field_StockTest extends PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock
     */
    protected $_model;

    /**
     * @var Magento_Data_Form_Element_Text
     */
    protected $_qty;

    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_factory = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $this->_qty = $this->getMock('Magento_Data_Form_Element_Text',
            array('getElementHtml', 'setForm', 'setValue', 'setName'), array($this->_factory)
        );
        $this->_model = $this->getMock('Magento_CatalogInventory_Block_Adminhtml_Form_Field_Stock',
            array('getElementHtml'),
            array($this->_factory, array('qty' => $this->_qty, 'name' => self::ATTRIBUTE_NAME))
        );
    }

    public function testGetElementHtml()
    {
        $this->_qty->expects($this->once())->method('getElementHtml')->will($this->returnValue('html'));
        $this->_model->expects($this->once())->method('getElementHtml')
            ->will($this->returnValue($this->_qty->getElementHtml()));
        $this->assertEquals('html', $this->_model->getElementHtml());
    }

    public function testSetForm()
    {
        $this->_qty->expects($this->once())->method('setForm')
            ->with($this->isInstanceOf('Magento_Data_Form_Element_Abstract'));
        $this->_model->setForm(new Magento_Data_Form_Element_Text($this->_factory));
    }

    public function testSetValue()
    {
        $value = array('qty' => 1, 'is_in_stock' => 0);
        $this->_qty->expects($this->once())->method('setValue')->with($this->equalTo(1));
        $this->_model->setValue($value);
    }

    public function testSetName()
    {
        $this->_qty->expects($this->once())->method('setName')->with(self::ATTRIBUTE_NAME . '[qty]');
        $this->_model->setName(self::ATTRIBUTE_NAME);
    }
}
