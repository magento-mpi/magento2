<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WriteService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Bundle\Model\SelectionFactory
     */
    protected $bundleSelectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Bundle\Model\Resource\BundleFactory
     */
    protected $bundleFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Bundle\Model\Resource\Option\CollectionFactory
     */
    protected $optionCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->productRepositoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductRepository', array(), array(), '', false
        );

        $this->bundleSelectionMock = $this->getMock(
            '\Magento\Bundle\Model\SelectionFactory', array(), array(), '', false
        );

        $this->bundleFactoryMock = $this->getMock(
            '\Magento\Bundle\Model\Resource\BundleFactory', array(), array(), '', false
        );

        $this->optionCollectionFactoryMock = $this->getMock(
            '\Magento\Bundle\Model\Resource\Option\CollectionFactory', array('create'), array(), '', false
        );

        $this->storeManagerMock = $this->getMock(
            '\Magento\Store\Model\StoreManagerInterface', array(), array(), '', false
        );

        $this->service = $helper->getObject('Magento\Bundle\Service\V1\Product\Link\WriteService',
            [
                'productRepository' => $this->productRepositoryMock,
                'bundleSelection' => $this->bundleSelectionMock,
                'bundleFactory' => $this->bundleFactoryMock,
                'optionCollection' => $this->optionCollectionFactoryMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testAddChildToNotBundleProduct()
    {
        $productLink = $this->getMock(
            'Magento\Bundle\Service\V1\Product\Link\Data\ProductLink', array(), array(), '', false
        );

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue(
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
        ));
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with('product_sku')
            ->will($this->returnValue($productMock));
        $this->service->addChild('product_sku', 1, $productLink);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testAddChildNonExistingOption()
    {
        $productLink = $this->getMock(
            'Magento\Bundle\Service\V1\Product\Link\Data\ProductLink', array(), array(), '', false
        );

        $productMock = $this->getMock('\Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->once())->method('getTypeId')->will($this->returnValue(
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
        ));
        $productMock->expects($this->once())->method('getId')->will($this->returnValue('product_id'));
        $this->productRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with('product_sku')
            ->will($this->returnValue($productMock));

        $store = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $store->expects($this->any())->method('getId')->will($this->returnValue(0));

        $option = $this->getMockBuilder('\Magento\Bundle\Model\Option')->disableOriginalConstructor()->getMock();
        $option->expects($this->any())->method('getOptionId')->will($this->returnValue(2));

        $optionsCollectionMock = $this->getMock(
            '\Magento\Bundle\Model\Resource\Option\Collection', array(), array(), '', false
        );
        $optionsCollectionMock->expects($this->once())
            ->method('setProductIdFilter')
            ->with($this->equalTo('product_id'))
            ->will($this->returnSelf());
        $optionsCollectionMock->expects($this->once())
            ->method('joinValues')
            ->with($this->equalTo(0))
            ->will($this->returnSelf());
        $optionsCollectionMock->expects($this->any())->method('getIterator')->will(
            $this->returnValue(new \ArrayIterator([$option]))
        );
        $this->optionCollectionFactoryMock->expects($this->any())->method('create')->will(
            $this->returnValue($optionsCollectionMock)
        );
        $this->service->addChild('product_sku', 1, $productLink);
    }
}
