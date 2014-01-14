<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Controller\Adminhtml;

class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\GroupedProduct\Controller\Adminhtml\Edit
     */
    protected $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    protected function setUp()
    {
        $this->request = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);
        $this->factory = $this->getMock('Magento\Catalog\Model\ProductFactory', array('create'), array(), '', false);
        $this->registry = $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false);
        $this->view = $this->getMock('Magento\App\ViewInterface', array(), array(), '', false);

        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->controller = $this->objectManager->getObject(
            '\Magento\GroupedProduct\Controller\Adminhtml\Edit',
            array(
                'request' => $this->request,
                'factory' => $this->factory,
                'registry' => $this->registry,
                'view' => $this->view,
            )
        );
    }

    public function testPopupActionNoProductId()
    {
        $storeId = 12;
        $typeId = 4;
        $productId = null;
        $setId = 0;
        $product = $this->getMock('Magento\Catalog\Model\Product',
            array('setStoreId', 'setTypeId', 'setData', '__wakeup'),
            array(), '', false);

        $this->request->expects($this->at(0))->method('getParam')->with('id')->will($this->returnValue($productId));
        $this->factory->expects($this->once())->method('create')->will($this->returnValue($product));
        $this->request->expects($this->at(1))->method('getParam')
            ->with('store', 0)->will($this->returnValue($storeId));

        $product->expects($this->once())->method('setStoreId')->with($storeId);
        $this->request->expects($this->at(2))->method('getParam')
            ->with('type')->will($this->returnValue($typeId));
        $product->expects($this->once())->method('setTypeId')->with($typeId);
        $product->expects($this->once())->method('setData')->with('_edit_mode', true);
        $this->request->expects($this->at(3))->method('getParam')->with('set')->will($this->returnValue($setId));
        $this->registry->expects($this->once())->method('register')->with('current_product', $product);

        $this->view->expects($this->once())->method('loadLayout')->with(false);
        $this->view->expects($this->once())->method('renderLayout');

        $this->controller->popupAction();
    }

    public function testPopupActionWithProductIdNoSetId()
    {
        $storeId = 12;
        $typeId = 4;
        $setId = 0;
        $productId = 399;
        $product = $this->getMock('Magento\Catalog\Model\Product',
            array('setStoreId', 'setTypeId', 'setData', 'load', '__wakeup'),
            array(), '', false);

        $this->request->expects($this->at(0))->method('getParam')->with('id')->will($this->returnValue($productId));
        $this->factory->expects($this->once())->method('create')->will($this->returnValue($product));
        $this->request->expects($this->at(1))->method('getParam')
            ->with('store', 0)->will($this->returnValue($storeId));
        $product->expects($this->once())->method('setStoreId')->with($storeId);
        $this->request->expects($this->at(2))->method('getParam')
            ->with('type')->will($this->returnValue($typeId));
        $product->expects($this->never())->method('setTypeId');
        $product->expects($this->once())->method('setData')->with('_edit_mode', true);
        $product->expects($this->once())->method('load')->with($productId);
        $this->request->expects($this->at(3))->method('getParam')->with('set')->will($this->returnValue($setId));
        $this->registry->expects($this->once())->method('register')->with('current_product', $product);

        $this->view->expects($this->once())->method('loadLayout')->with(false);
        $this->view->expects($this->once())->method('renderLayout');

        $this->controller->popupAction();
    }
}