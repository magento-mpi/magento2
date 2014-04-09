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
namespace Magento\Catalog\Controller;

use Magento\App\Action\Action;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryHelper;

    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \Magento\Core\Model\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layout;

    /**
     * @var \Magento\View\Layout\ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $update;

    /**
     * @var \Magento\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $category;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Core\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $store;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Design|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogDesign;

    /**
     * @var \Magento\Theme\Helper\Layout|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutHelper;

    /**
     * @var \Magento\Catalog\Controller\Category
     */
    protected $controller;

    public function setUp()
    {
        $this->request = $this->getMock('Magento\App\RequestInterface');
        $this->response = $this->getMock('Magento\App\ResponseInterface');

        $this->categoryHelper = $this->getMock('Magento\Catalog\Helper\Category', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\ObjectManager', [], [], '', false);
        $this->eventManager = $this->getMock('Magento\Event\ManagerInterface');

        $this->update = $this->getMock('Magento\View\Layout\ProcessorInterface');
        $this->layout = $this->getMock('Magento\Core\Model\Layout', [], [], '', false);
        $this->layout->expects($this->any())->method('getUpdate')->will($this->returnValue($this->update));
        $this->view = $this->getMock('Magento\App\ViewInterface');
        $this->view->expects($this->any())->method('getLayout')->will($this->returnValue($this->layout));

        $this->context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $this->context->expects($this->any())->method('getRequest')->will($this->returnValue($this->request));
        $this->context->expects($this->any())->method('getResponse')->will($this->returnValue($this->response));
        $this->context->expects($this->any())->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $this->context->expects($this->any())->method('getEventManager')->will($this->returnValue($this->eventManager));
        $this->context->expects($this->any())->method('getView')->will($this->returnValue($this->view));

        $this->category = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $this->categoryFactory = $this->getMock('Magento\Catalog\Model\CategoryFactory', ['create'], [], '', false);

        $this->store = $this->getMock('Magento\Core\Model\Store', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Core\Model\StoreManagerInterface');
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->store));

        $this->catalogDesign = $this->getMock('Magento\Catalog\Model\Design', [], [], '', false);
        $this->layoutHelper = $this->getMock('Magento\Theme\Helper\Layout', [], [], '', false);

        $this->controller = (new ObjectManager($this))->getObject('Magento\Catalog\Controller\Category', [
            'context' => $this->context,
            'categoryFactory' => $this->categoryFactory,
            'storeManager' => $this->storeManager,
            'catalogDesign' => $this->catalogDesign,
        ]);
    }

    public function testApplyCustomLayoutUpdate()
    {
        $categoryId = 123;
        $pageLayout = 'page_layout';

        $this->objectManager->expects($this->any())->method('get')->will($this->returnValueMap([
            ['Magento\Catalog\Helper\Category', $this->categoryHelper],
            ['Magento\Theme\Helper\Layout', $this->layoutHelper],
        ]));

        $this->request->expects($this->any())->method('getParam')->will($this->returnValueMap([
            [Action::PARAM_NAME_URL_ENCODED],
            ['id', false, $categoryId],
        ]));

        $this->categoryFactory->expects($this->any())->method('create')->will($this->returnValue($this->category));
        $this->category->expects($this->any())->method('setStoreId')->will($this->returnSelf());
        $this->category->expects($this->any())->method('load')->with($categoryId)->will($this->returnSelf());

        $this->categoryHelper->expects($this->any())->method('canShow')->will($this->returnValue(true));

        $settings = $this->getMock('Magento\Object', ['getPageLayout'], [], '', false);
        $settings->expects($this->atLeastOnce())->method('getPageLayout')->will($this->returnValue($pageLayout));

        $this->catalogDesign->expects($this->any())->method('getDesignSettings')->will($this->returnValue($settings));

        $this->layoutHelper->expects($this->once())->method('applyHandle')->with($pageLayout);

        $this->controller->viewAction();
    }
}
