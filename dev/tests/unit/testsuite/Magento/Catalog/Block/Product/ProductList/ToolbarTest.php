<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product\ProductList;

class ToolbarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $model;

    /**
     * @var \Magento\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Catalog\Helper\Data | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeConfig;
    /**
     * @var \Magento\Catalog\Model\Config | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogConfig;

    /**
     * @var \Magento\Catalog\Helper\Product\ProductList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productListHelper;

    protected function setUp()
    {
        $this->model = $this->getMock(
            'Magento\Catalog\Model\Product\ProductList\Toolbar',
            array(
                'getDirection',
                'getOrder',
                'getMode',
                'getLimit',
                'getCurrentPage'
            ),
            array(),
            '',
            false
        );
        $this->urlBuilder = $this->getMock('Magento\Url', array('getUrl'), array(), '', false);
        $this->storeConfig = $this->getMock('Magento\App\Config\ScopeConfigInterface');

        $storeConfig = array(
            array(\Magento\Catalog\Model\Config::XML_PATH_LIST_DEFAULT_SORT_BY, null, 'name'),
            array(\Magento\Catalog\Helper\Product\ProductList::XML_PATH_LIST_MODE, null, 'grid-list'),
            array('catalog/frontend/list_per_page_values', null, '10,20,30'),
            array('catalog/frontend/grid_per_page_values', null, '10,20,30'),
            array('catalog/frontend/list_allow_all', null, false)
        );

        $this->storeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($storeConfig));

        $this->catalogConfig = $this->getMock(
            'Magento\Catalog\Model\Config',
            array('getAttributeUsedForSortByArray'),
            array(),
            '',
            false
        );
        $this->catalogConfig->expects($this->any())
            ->method('getAttributeUsedForSortByArray')
            ->will($this->returnValue(array('name' => array(), 'price' => array())));

        $context = $this->getMock(
            'Magento\View\Element\Template\Context',
            array('getUrlBuilder', 'getStoreConfig'),
            array(),
            '',
            false
        );
        $context->expects($this->any())
            ->method('getUrlBuilder')
            ->will($this->returnValue($this->urlBuilder));
        $context->expects($this->any())
            ->method('getStoreConfig')
            ->will($this->returnValue($this->storeConfig));

        $this->productListHelper = $this->getMock('Magento\Catalog\Helper\Product\ProductList',
            array(),
            array(),
            '',
            false
        );
        $this->productListHelper->expects($this->any())
            ->method('getAvailableViewMode')
            ->will($this->returnValue(array('list' => 'List')));

        $this->helper = $this->getMock('Magento\Catalog\Helper\Data', array('urlEncode'), array(), '', false);
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->block = $objectManager->getObject(
            'Magento\Catalog\Block\Product\ProductList\Toolbar',
            array(
                'context' => $context,
                'catalogConfig' => $this->catalogConfig,
                'toolbarModel' => $this->model,
                'helper' => $this->helper,
                'productListHelper' => $this->productListHelper
            )
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetCurrentPage()
    {
        $page = 3;

        $this->model->expects($this->once())
            ->method('getCurrentPage')
            ->will($this->returnValue($page));
        $this->assertEquals($page, $this->block->getCurrentPage());
    }

    public function testGetPagerEncodedUrl()
    {
        $url = 'url';
        $encodedUrl = '123';

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($url));
        $this->helper->expects($this->once())
            ->method('urlEncode')
            ->with($url)
            ->will($this->returnValue($encodedUrl));
        $this->assertEquals($encodedUrl, $this->block->getPagerEncodedUrl());
    }

    public function testGetCurrentOrder()
    {
        $order = 'price';
        $this->model->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($order));

        $this->assertEquals($order, $this->block->getCurrentOrder());
    }

    public function testGetCurrentDirection()
    {
        $direction = 'desc';

        $this->model->expects($this->once())
            ->method('getDirection')
            ->will($this->returnValue($direction));

        $this->assertEquals($direction, $this->block->getCurrentDirection());
    }

    public function testGetCurrentMode()
    {
        $mode = 'list';

        $this->model->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));

        $this->assertEquals($mode, $this->block->getCurrentMode());
    }

    public function testGetLimit()
    {
        $mode = 'list';
        $limit = 10;

        $this->model->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));

        $this->model->expects($this->once())
            ->method('getLimit')
            ->will($this->returnValue($limit));
        $this->productListHelper->expects($this->once())
            ->method('getAvailableLimit')
            ->will($this->returnValue(array(10 => 10, 20 => 20)));
        $this->productListHelper->expects($this->once())
            ->method('getDefaultLimitPerPageValue')
            ->with($this->equalTo('list'))
            ->will($this->returnValue(10));

        $this->assertEquals($limit, $this->block->getLimit());
    }
}
