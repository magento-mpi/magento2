<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\CopyConstructor;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Model\Product\CopyConstructor\Grouped
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_duplicateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_linkMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_linkCollectionMock;

    protected function setUp()
    {
        $this->_model = new \Magento\GroupedProduct\Model\Product\CopyConstructor\Grouped();

        $this->_productMock   = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('getTypeId', '__wakeup', 'getLinkInstance'),
            array(),
            '',
            false
        );

        $this->_duplicateMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('setGroupedLinkData', '__wakeup'),
            array(),
            '',
            false
        );

        $this->_linkMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Link',
            array('setLinkTypeId', '__wakeup', 'getAttributes', 'getLinkCollection'),
            array(),
            '',
            false
        );

        $this->_productMock->expects($this->any())
            ->method('getLinkInstance')
            ->will($this->returnValue($this->_linkMock));
    }

    public function testBuildWithNonGroupedProductType()
    {
        $this->_productMock->expects($this->once())->method('getTypeId')->will($this->returnValue('some value'));

        $this->_duplicateMock->expects($this->never())->method('setGroupedLinkData');

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }

    public function testBuild()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $expectedData = array(
            '100500' => array('some' => 'data')
        );

        $this->_productMock->expects($this->once())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE));

        $attributes = array(
            'attributeOne' => array('code' => 'one'),
            'attributeTwo' => array('code' => 'two'),
        );

        $this->_linkMock->expects($this->once())->method('getAttributes')->will($this->returnValue($attributes));

        $productLinkMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Link',
            array('__wakeup', 'getLinkedProductId', 'toArray'),
            array(),
            '',
            false
        );
        $this->_linkMock->expects($this->atLeastOnce())->method('setLinkTypeId')
            ->with(\Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED);

        $productLinkMock->expects($this->once())->method('getLinkedProductId')->will($this->returnValue('100500'));
        $productLinkMock->expects($this->once())
            ->method('toArray')
            ->with(array('one', 'two'))
            ->will($this->returnValue(array('some' => 'data')));

        $collectionMock = $helper->getCollectionMock(
            '\Magento\Catalog\Model\Resource\Product\Link\Collection',
            array($productLinkMock)
        );
        $collectionMock->expects($this->once())->method('setProduct')->with($this->_productMock);
        $collectionMock->expects($this->once())->method('addLinkTypeIdFilter');
        $collectionMock->expects($this->once())->method('addProductIdFilter');
        $collectionMock->expects($this->once())->method('joinAttributes');

        $this->_linkMock->expects($this->once())
            ->method('getLinkCollection')
            ->will($this->returnValue($collectionMock));

        $this->_duplicateMock->expects($this->once())
            ->method('setGroupedLinkData')
            ->with($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }
}
