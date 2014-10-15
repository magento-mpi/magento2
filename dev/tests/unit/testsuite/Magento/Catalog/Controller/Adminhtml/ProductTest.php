<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml;

abstract class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Controller\Product
     */
    protected $action;

    /**
     *  Init context object
     */
    protected function initContext()
    {
        $productActionMock = $this->getMock('Magento\Catalog\Model\Product\Action', array(), array(), '', false);
        $objectManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\ObjectManager',
            array(),
            '',
            true,
            true,
            true,
            array('get')
        );
        $objectManagerMock->expects($this->any())->method('get')->will($this->returnValue($productActionMock));

        $block = $this->getMockBuilder('\Magento\Framework\View\Element\AbstractBlock')
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $layout = $this->getMockBuilder('Magento\Framework\View\Layout\Element\Layout')
            ->setMethods(['getBlock'])->disableOriginalConstructor()
            ->getMock();
        $layout->expects($this->any())->method('getBlock')->will($this->returnValue($block));
        $view = $this->getMockBuilder('Magento\Framework\App\View')
            ->setMethods(['loadLayout', 'getLayout', 'renderLayout'])
            ->disableOriginalConstructor()->getMock();
        $view->expects($this->any())->method('renderLayout')->will($this->returnSelf());
        $view->expects($this->any())->method('getLayout')->will($this->returnValue($layout));
        $view->expects($this->any())->method('loadLayout')->with(array(
            'default',
            'popup',
            'catalog_product_new',
            'catalog_product_simple'
        ))->will($this->returnSelf());

        $eventManager = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->setMethods(['dispatch'])->disableOriginalConstructor()->getMock();
        $eventManager->expects($this->any())->method('dispatch')->will($this->returnSelf());
        $title = $this->getMockBuilder('\Magento\Framework\App\Action\Title')
            ->setMethods(['add'])->disableOriginalConstructor()->getMock();
        $title->expects($this->any())->method('add')->withAnyParameters()->will($this->returnSelf());
        $requestInterfaceMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')->setMethods(
            array('getParam', 'getFullActionName')
        )->disableOriginalConstructor()->getMock();

        $responseInterfaceMock = $this->getMockBuilder('Magento\Framework\App\ResponseInterface')->setMethods(
            array('setRedirect', 'sendResponse')
        )->getMock();

        $managerInterfaceMock = $this->getMock('Magento\Framework\Message\ManagerInterface');
        $sessionMock = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);
        $actionFlagMock = $this->getMock('Magento\Framework\App\ActionFlag', array(), array(), '', false);
        $helperDataMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $this->context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            array(
                'getRequest',
                'getResponse',
                'getObjectManager',
                'getEventManager',
                'getMessageManager',
                'getSession',
                'getActionFlag',
                'getHelper',
                'getTitle',
                'getView'
            ),
            array(),
            '',
            false
        );

        $this->context->expects($this->any())->method('getTitle')->will($this->returnValue($title));
        $this->context->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $this->context->expects($this->any())->method('getView')->will($this->returnValue($view));
        $this->context->expects($this->any())->method('getRequest')->will($this->returnValue($requestInterfaceMock));
        $this->context->expects($this->any())->method('getResponse')->will($this->returnValue($responseInterfaceMock));
        $this->context->expects($this->any())->method('getObjectManager')->will($this->returnValue($objectManagerMock));

        $this->context->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($managerInterfaceMock));
        $this->context->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $this->context->expects($this->any())->method('getActionFlag')->will($this->returnValue($actionFlagMock));
        $this->context->expects($this->any())->method('getHelper')->will($this->returnValue($helperDataMock));
        return $this->context;
    }
}
