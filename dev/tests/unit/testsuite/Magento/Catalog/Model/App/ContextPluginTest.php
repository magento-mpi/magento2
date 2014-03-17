<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\App;

/**
 * Class ContextPluginTest
 */
class ContextPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContextPlugin
     */
    protected $plugin;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $toolbarModelMock;

    /**
     * @var \Magento\App\Http\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpContextMock;

    /**
     * @var \Magento\App\FrontController|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $frontControllerMock;

    /**
     * @var \Magento\Catalog\Helper\Product\ProductList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productListHelperMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->toolbarModelMock = $this->getMock(
            'Magento\Catalog\Model\Product\ProductList\Toolbar',
            array(
                'getDirection',
                'getOrder',
                'getMode',
                'getLimit'
            ),
            array(),
            '',
            false
        );
        $this->frontControllerMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->httpContextMock = $this->getMock('Magento\App\Http\Context', array(), array(), '', false);
        $this->productListHelperMock = $this->getMock('Magento\Catalog\Helper\Product\ProductList',
            array(), array(), '', false);
        $this->plugin = new ContextPlugin(
            $this->toolbarModelMock,
            $this->httpContextMock,
            $this->productListHelperMock
        );
    }

    public function testBeforeDispatchHasSortDirection()
    {
        $this->toolbarModelMock->expects($this->exactly(1))
            ->method('getDirection')
            ->will($this->returnValue('asc'));
        $this->toolbarModelMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue('Name'));
        $this->toolbarModelMock->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue('list'));
        $this->toolbarModelMock->expects($this->once())
            ->method('getLimit')
            ->will($this->returnValue(array(1 => 1, 2 => 2)));
        $this->productListHelperMock->expects($this->once())
            ->method('getDefaultSortField')
            ->will($this->returnValue('Field'));
        $this->productListHelperMock->expects($this->exactly(2))
            ->method('getDefaultViewMode')
            ->will($this->returnValue('grid'));
        $this->productListHelperMock->expects($this->once())
            ->method('getDefaultLimitPerPageValue')
            ->will($this->returnValue(array(10=>10)));
        $this->httpContextMock->expects($this->exactly(4))
            ->method('setValue')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_DIRECTION,
                    'asc',
                    \Magento\Catalog\Helper\Product\ProductList::DEFAULT_SORT_DIRECTION,
                    $this->httpContextMock
                ), array(
                    \Magento\Catalog\Helper\Data::CONTEXT_CATALOG_SORT_ORDER,
                    'Name',
                    'Field',
                    $this->httpContextMock
                ), array(
                    \Magento\Catalog\Helper\Data::CONTEXT_CATALOG_DISPLAY_MODE,
                    'list',
                    'grid',
                    $this->httpContextMock
                ), array(
                    \Magento\Catalog\Helper\Data::CONTEXT_CATALOG_LIMIT,
                    array(1 => 1, 2 => 2), array (10 => 10)
                )
            )));
        $this->assertNull($this->plugin->beforeDispatch($this->frontControllerMock));
    }
}
