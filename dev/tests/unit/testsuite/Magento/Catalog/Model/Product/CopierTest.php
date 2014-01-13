<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class CopierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Copier
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $copyConstructorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    
    protected function setUp()
    {
        $this->copyConstructorMock = $this->getMock('\Magento\Catalog\Model\Product\CopyConstructorInterface');
        $this->storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false
        );
        $this->productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue('1'));
        $this->productMock->expects($this->any())->method('getData')->will($this->returnValue('product data'));

        $this->_model = new Copier($this->copyConstructorMock, $this->productFactoryMock, $this->storeManagerMock);
    }

    public function testCopy()
    {
        $this->productMock->expects($this->atLeastOnce())->method('getWebsiteIds');
        $this->productMock->expects($this->atLeastOnce())->method('getCategoryIds');
        $storeMock = $this->getMock('\Magento\Core\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->atLeastOnce())->method('getId')->will($this->returnValue('storeId'));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $resourceMock = $this->getMock('\Magento\Catalog\Model\Resource\Product', array(), array(), '', false);
        $optionMock = $this->getMock('\Magento\Catalog\Model\Product\Option', array(), array(), '', false);
        $this->productMock->expects($this->once())
            ->method('getResource')->will($this->returnValue($resourceMock));
        $this->productMock->expects($this->once())
            ->method('getOptionInstance')->will($this->returnValue($optionMock));

        $duplicateMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('__wakeup', 'setData', 'setIsDuplicate', 'setOriginalId', 'setStatus', 'setCreatedAt', 'setUpdatedAt',
                'setId', 'setStoreId', 'getId', 'save'
            ),
            array(),
            '',
            false
        );
        $this->productFactoryMock->expects($this->once())->method('create')->will($this->returnValue($duplicateMock));

        $duplicateMock->expects($this->once())->method('setIsDuplicate')->with(true);
        $duplicateMock->expects($this->once())->method('setOriginalId')->with(1);
        $duplicateMock->expects($this->once())->method('setStatus')
            ->with(\Magento\Catalog\Model\Product\Status::STATUS_DISABLED);
        $duplicateMock->expects($this->once())->method('setCreatedAt')->with(null);
        $duplicateMock->expects($this->once())->method('setUpdatedAt')->with(null);
        $duplicateMock->expects($this->once())->method('setId')->with(null);
        $duplicateMock->expects($this->once())->method('setStoreId')->with('storeId');
        $duplicateMock->expects($this->once())->method('setData')->with('product data');



        $this->copyConstructorMock->expects($this->once())->method('build')->with($this->productMock, $duplicateMock);

        $duplicateMock->expects($this->once())->method('save');
        $duplicateMock->expects($this->any())->method('getId')->will($this->returnValue(2));

        $optionMock->expects($this->once())->method('duplicate')->with(1, 2);
        $resourceMock->expects($this->once())->method('duplicate')->with(1, 2);
        $this->assertEquals($duplicateMock, $this->_model->copy($this->productMock));
    }
}
