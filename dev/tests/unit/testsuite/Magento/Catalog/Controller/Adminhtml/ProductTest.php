<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product
     */
    protected $_controller;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_priceProcessor;

    public function setUp()
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
        $eventManager = $this->getMockBuilder('Magento\Framework\Event\Manager')
            ->setMethods(['dispatch'])->disableOriginalConstructor()->getMock();
        $eventManager->expects($this->any())->method('dispatch')->will($this->returnSelf());

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
            'popup',
            'catalog_product_new',
            'catalog_product_simple'
        ))->will($this->returnSelf());

        $this->_priceProcessor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor',
            array(),
            array(),
            '',
            false
        );
        $requestInterfaceMock = $this->getMock(
            'Magento\Framework\App\Request\Http',
            array('getParam', 'getFullActionName'),
            array(),
            '',
            false
        );

        $responseInterfaceMock = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            array('setRedirect', 'sendResponse')
        );
        $managerInterfaceMock = $this->getMock(
            'Magento\Framework\Message\ManagerInterface',
            array(),
            array(),
            '',
            false
        );
        $sessionMock = $this->getMock('Magento\Backend\Model\Session', array(), array(), '', false);
        $actionFlagMock = $this->getMock('Magento\Framework\App\ActionFlag', array(), array(), '', false);
        $helperDataMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $contextMock = $this->getMock(
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
        $title = $this->getMockBuilder('\Magento\Framework\App\Action\Title')
            ->setMethods(['add'])->disableOriginalConstructor()->getMock();
        $title->expects($this->any())->method('add')->withAnyParameters()->will($this->returnSelf());

        $contextMock->expects($this->any())->method('getTitle')->will($this->returnValue($title));
        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($requestInterfaceMock));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($responseInterfaceMock));
        $contextMock->expects($this->any())->method('getObjectManager')->will($this->returnValue($objectManagerMock));
        $contextMock->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $contextMock->expects($this->any())->method('getView')->will($this->returnValue($view));
        $contextMock->expects($this->any())
            ->method('getMessageManager')
            ->will($this->returnValue($managerInterfaceMock));
        $contextMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $contextMock->expects($this->any())->method('getActionFlag')->will($this->returnValue($actionFlagMock));
        $contextMock->expects($this->any())->method('getHelper')->will($this->returnValue($helperDataMock));
        $productBuilder = $this->getMockBuilder('Magento\Catalog\Controller\Adminhtml\Product\Builder')->setMethods([
            'build'
        ])->disableOriginalConstructor()->getMock();

        $product = $this->getMockBuilder('\Magento\Catalog\Model\Product')->disableOriginalConstructor()
            ->setMethods(['getTypeId', 'getStoreId', '__sleep', '__wakeup'])->getMock();
        $product->expects($this->any())->method('getTypeId')->will($this->returnValue('simple'));
        $product->expects($this->any())->method('getStoreId')->will($this->returnValue('1'));
        $productBuilder->expects($this->any())->method('build')->will($this->returnValue($product));

        $this->_controller = new \Magento\Catalog\Controller\Adminhtml\Product(
            $contextMock,
            $this->getMock('Magento\Framework\Registry', array(), array(), '', false),
            $this->getMock('Magento\Framework\Stdlib\DateTime\Filter\Date', array(), array(), '', false),
            $this->getMock(
                'Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper',
                array(),
                array(),
                '',
                false
            ),
            $this->getMock(
                'Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter',
                array(),
                array(),
                '',
                false
            ),
            $this->getMock('Magento\Catalog\Model\Product\Copier', array(), array(), '', false),
            $productBuilder,
            $this->getMock('Magento\Catalog\Model\Product\Validator', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\TypeTransitionManager', array(), array(), '', false),
            $this->_priceProcessor
        );
    }

    public function testMassStatusAction()
    {
        $this->_priceProcessor->expects($this->once())->method('reindexList');

        $this->_controller->massStatusAction();
    }

    /**
     * Testing `newAction` method
     */
    public function testNewAction()
    {
        $this->_controller->getRequest()->expects($this->at(0))->method('getParam')
            ->with('set')->will($this->returnValue(true));
        $this->_controller->getRequest()->expects($this->at(1))->method('getParam')
            ->with('popup')->will($this->returnValue(true));
        $this->_controller->getRequest()->expects($this->any())->method('getFullActionName')
            ->will($this->returnValue('catalog_product_new'));
        $this->_controller->newAction();
    }
}
