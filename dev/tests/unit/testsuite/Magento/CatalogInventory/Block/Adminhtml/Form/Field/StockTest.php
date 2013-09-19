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

namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

class StockTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock
     */
    protected $_model;

    /**
     * @var \Magento\Data\Form\Element\Text
     */
    protected $_qty;

    /**
     * @var \Magento\Data\Form\Element\Factory
     */
    protected $_factory;

    /**
     * @var \Magento\Data\Form\Element\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    protected function setUp()
    {
        $this->_coreHelper = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->_factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $this->_qty = $this->getMock('Magento\Data\Form\Element\Text',
            array('getElementHtml', 'setForm', 'setValue', 'setName'),
            array($this->_coreHelper, $this->_factory, $this->_collectionFactory)
        );
        $this->_model = $this->getMock('Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock',
            array('getElementHtml'),
            array(
                $this->_coreHelper,
                $this->_factory,
                $this->_collectionFactory,
                array('qty' => $this->_qty, 'name' => self::ATTRIBUTE_NAME)
            )
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
            ->with($this->isInstanceOf('Magento\Data\Form\Element\AbstractElement'));
        $this->_model->setForm(
            new \Magento\Data\Form\Element\Text($this->_coreHelper, $this->_factory, $this->_collectionFactory)
        );
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
