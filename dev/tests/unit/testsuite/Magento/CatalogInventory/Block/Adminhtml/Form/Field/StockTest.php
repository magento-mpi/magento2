<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StockTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var \Magento\Core\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_coreHelperMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryElementMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactoryMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_qtyMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\TextFactory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryTextMock;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_block;

    protected function setUp()
    {
        $this->_factoryElementMock = $this->getMock('Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $this->_collectionFactoryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_qtyMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Text',
            array('setForm', 'setValue', 'setName'),
            array(),
            '',
            false
        );
        $this->_factoryTextMock = $this->getMock('Magento\Framework\Data\Form\Element\TextFactory', array('create'));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $objectManagerHelper->getObject(
            'Magento\CatalogInventory\Block\Adminhtml\Form\Field\Stock',
            array(
                'factoryElement' => $this->_factoryElementMock,
                'factoryCollection' => $this->_collectionFactoryMock,
                'factoryText' => $this->_factoryTextMock,
                'data' => array('qty' => $this->_qtyMock, 'name' => self::ATTRIBUTE_NAME)
            )
        );
    }

    public function testSetForm()
    {
        $this->_qtyMock->expects(
            $this->once()
        )->method(
            'setForm'
        )->with(
            $this->isInstanceOf('Magento\Framework\Data\Form\Element\AbstractElement')
        );

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block->setForm(
            $objectManager->getObject(
                'Magento\Framework\Data\Form\Element\Text',
                array(
                    'factoryElement' => $this->_factoryElementMock,
                    'factoryCollection' => $this->_collectionFactoryMock
                )
            )
        );
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
