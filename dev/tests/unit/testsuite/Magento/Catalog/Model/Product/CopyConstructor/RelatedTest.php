<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\CopyConstructor;

class RelatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \\Magento\Catalog\Model\Product\CopyConstructor\Related
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
        $this->_model = new \Magento\Catalog\Model\Product\CopyConstructor\Related();

        $this->_productMock   = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array(),
            array(),
            '',
            false
        );

        $this->_duplicateMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('setRelatedLinkData', '__wakeup'),
            array(),
            '',
            false
        );

        $this->_linkMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Link',
            array('__wakeup', 'getAttributes', 'getRelatedLinkCollection', 'useRelatedLinks'),
            array(),
            '',
            false
        );

        $this->_productMock->expects($this->any())
            ->method('getLinkInstance')
            ->will($this->returnValue($this->_linkMock));
    }

    public function testBuild()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $expectedData = array(
            '100500' => array('some' => 'data')
        );

        $attributes = array(
            'attributeOne' => array('code' => 'one'),
            'attributeTwo' => array('code' => 'two'),
        );

        $this->_linkMock->expects($this->once())->method('useRelatedLinks');

        $this->_linkMock->expects($this->once())->method('getAttributes')->will($this->returnValue($attributes));

        $productLinkMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Link',
            array('__wakeup', 'getLinkedProductId', 'toArray'),
            array(),
            '',
            false
        );

        $productLinkMock->expects($this->once())->method('getLinkedProductId')->will($this->returnValue('100500'));
        $productLinkMock->expects($this->once())
            ->method('toArray')
            ->with(array('one', 'two'))
            ->will($this->returnValue(array('some' => 'data')));

        $collectionMock = $helper->getCollectionMock(
            '\Magento\Catalog\Model\Resource\Product\Link\Collection',
            array($productLinkMock)
        );
        $this->_productMock->expects($this->once())
            ->method('getRelatedLinkCollection')
            ->will($this->returnValue($collectionMock));

        $this->_duplicateMock->expects($this->once())
            ->method('setRelatedLinkData')
            ->with($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }
}
