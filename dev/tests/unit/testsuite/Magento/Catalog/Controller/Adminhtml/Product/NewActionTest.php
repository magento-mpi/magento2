<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class NewActionTest extends \Magento\Catalog\Controller\Adminhtml\ProductTest
{
    protected $action;

    protected function setUp()
    {
        $productBuilder = $this->getMockBuilder('Magento\Catalog\Controller\Adminhtml\Product\Builder')->setMethods([
                'build'
            ])->disableOriginalConstructor()->getMock();

        $product = $this->getMockBuilder('\Magento\Catalog\Model\Product')->disableOriginalConstructor()
            ->setMethods(['getTypeId', 'getStoreId', '__sleep', '__wakeup'])->getMock();
        $product->expects($this->any())->method('getTypeId')->will($this->returnValue('simple'));
        $product->expects($this->any())->method('getStoreId')->will($this->returnValue('1'));
        $productBuilder->expects($this->any())->method('build')->will($this->returnValue($product));

        $this->action = new \Magento\Catalog\Controller\Adminhtml\Product\NewAction(
            $this->initContext(),
            $productBuilder,
            $this->getMockBuilder('Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter')
                ->disableOriginalConstructor()->getMock()
        );

    }

    /**
     * Testing `newAction` method
     */
    public function testExecute()
    {
        $this->action->getRequest()->expects($this->at(0))->method('getParam')
            ->with('set')->will($this->returnValue(true));
        $this->action->getRequest()->expects($this->at(1))->method('getParam')
            ->with('popup')->will($this->returnValue(true));
        $this->action->getRequest()->expects($this->any())->method('getFullActionName')
            ->will($this->returnValue('catalog_product_new'));
        $this->action->execute();
    }
}
