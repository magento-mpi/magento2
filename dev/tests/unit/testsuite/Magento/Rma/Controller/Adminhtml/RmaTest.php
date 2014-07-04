<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Controller\Adminhtml;

/**
 * Class RmaTest
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class RmaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected $controller;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\Response\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $flagActionMock;

    /**
     * @var \Magento\Rma\Model\Resource\Item\Collection
     */
    protected $rmaCollectionMock;

    /**
     * @var \Magento\Rma\Model\Item
     */
    protected $rmaItemMock;

    /**
     * @var \Magento\Rma\Model\Rma
     */
    protected $rmaModelMock;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderMock;

    /**
     * @var \Magento\Rma\Model\Rma\Source\Status
     */
    protected $sourceStatusMock;

    /**
     * @var \Magento\Rma\Model\Rma\Status\History
     */
    protected $statusHistoryMock;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $viewMock;

    /**
     * @var \Magento\Framework\App\Action\Title
     */
    protected $titleMock;

    /**
     * @var \Magento\Framework\Data\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formMock;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * test setUp
     */
    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->viewMock = $this->getMock('Magento\Framework\App\ViewInterface', [], [], '', false);
        $this->titleMock = $this->getMock('Magento\Framework\App\Action\Title', [], [], '', false);
        $this->formMock = $this->getMock('Magento\Framework\Data\Form', ['hasNewAttributes', 'toHtml'], [], '', false);
        $this->helperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->initMocks();
        $contextMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));
        $contextMock->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManagerMock));
        $contextMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($this->sessionMock));
        $contextMock->expects($this->once())
            ->method('getActionFlag')
            ->will($this->returnValue($this->flagActionMock));
        $contextMock->expects($this->once())
            ->method('getHelper')
            ->will($this->returnValue($backendHelperMock));
        $contextMock->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($this->viewMock));
        $contextMock->expects($this->once())
            ->method('getTitle')
            ->will($this->returnValue($this->titleMock));

        $this->controller = $objectManager->getObject(
            'Magento\Rma\Controller\Adminhtml\Rma',
            [
                'context' => $contextMock,
                'coreRegistry' => $this->coreRegistryMock
            ]
        );
    }

    protected function initMocks()
    {
        $this->coreRegistryMock = $this->getMock('Magento\Framework\Registry', [], [], '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->responseMock = $this->getMock(
            'Magento\Framework\App\Response\Http',
            [
                'setBody',
                'representJson',
                'setRedirect',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $this->messageManagerMock = $this->getMock('Magento\Framework\Message\ManagerInterface', [], [], '', false);
        $this->sessionMock = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $this->flagActionMock = $this->getMock('Magento\Framework\App\ActionFlag', [], [], '', false);
        $this->rmaCollectionMock = $this->getMock('Magento\Rma\Model\Resource\Item\Collection', [], [], '', false);
        $this->rmaItemMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $this->rmaModelMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            [
                'saveRma',
                'getId',
                'setStatus',
                'load',
                'canClose',
                'close',
                'save',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $this->sourceStatusMock = $this->getMock('Magento\Rma\Model\Rma\Source\Status', [], [], '', false);
        $this->statusHistoryMock = $this->getMock(
            'Magento\Rma\Model\Rma\Status\History',
            [
                'setRma',
                'sendNewRmaEmail',
                'saveComment',
                'saveSystemComment',
                'setComment',
                'sendAuthorizeEmail',
                'sendCommentEmail',
                '__wakeup'
            ],
            [],
            '',
            false
        );
        $this->objectManagerMock->expects($this->any())
            ->method('create')
            ->will(
                $this->returnValueMap(
                    [
                        ['Magento\Rma\Model\Resource\Item\Collection', [], $this->rmaCollectionMock],
                        ['Magento\Rma\Model\Item', [], $this->rmaItemMock],
                        ['Magento\Rma\Model\Rma', [], $this->rmaModelMock],
                        ['Magento\Sales\Model\Order', [], $this->orderMock],
                        ['Magento\Rma\Model\Rma\Source\Status', [], $this->sourceStatusMock],
                        ['Magento\Rma\Model\Rma\Status\History', [], $this->statusHistoryMock]
                    ]
                )
            );
    }

    public function testIndexAction()
    {
        $layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            ['renderLayout', 'getBlock'],
            [],
            '',
            false
        );
        $blockMock = $this->getMock('Magento\Backend\Block\Menu', ['setActive', 'getMenuModel'], [], '', false);
        $menuModelMock = $this->getMock('Magento\Backend\Model\Menu', [], [], '', false);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('menu')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setActive')
            ->with('Magento_Rma::sales_magento_rma_rma');
        $blockMock->expects($this->once())
            ->method('getMenuModel')
            ->will($this->returnValue($menuModelMock));
        $menuModelMock->expects($this->once())
            ->method('getParentItems')
            ->will($this->returnValue([]));
        $this->titleMock->expects($this->once())
            ->method('add')
            ->with(__('Returns'));
        $this->viewMock->expects($this->once())
            ->method('renderLayout');

        $this->assertNull($this->controller->indexAction());
    }

    public function testSaveNewAction()
    {
        $dateTimeModelMock = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $commentText = 'some comment';
        $visibleOnFront = true;

        $this->initRequestData($commentText, $visibleOnFront);

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Stdlib\DateTime\DateTime')
            ->will($this->returnValue($dateTimeModelMock));
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($this->orderMock));
        $this->rmaModelMock->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())->method('sendNewRmaEmail');
        $this->statusHistoryMock->expects($this->once())
            ->method('saveComment')
            ->with($commentText, $visibleOnFront, true);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('You submitted the RMA request.'));

        $this->assertNull($this->controller->saveNewAction());
    }

    protected function initRequestData($commentText = '', $visibleOnFront = true)
    {
        $rmaConfirmation = true;
        $this->requestMock->expects($this->once())
            ->method('isPost')
            ->will($this->returnValue(true));
        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->will(
                $this->returnValue(
                    [
                        'items' => [],
                        'rma_confirmation' => $rmaConfirmation,
                        'comment' => [
                            'comment' => $commentText,
                            'is_visible_on_front' => $visibleOnFront,
                        ]
                    ]
                )
            );
    }

    public function testSaveAction()
    {
        $rmaId = 1;
        $commentText = 'some comment';
        $visibleOnFront = true;

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['rma_id', null, $rmaId]
                    ]
                )
            );
        $this->initRequestData($commentText, $visibleOnFront);

        $this->rmaCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('rma_entity_id', $rmaId)
            ->will($this->returnValue([$this->rmaItemMock]));
        $this->rmaItemMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($rmaId));
        $this->rmaModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($rmaId));
        $this->rmaModelMock->expects($this->any())
            ->method('setStatus')
            ->will($this->returnSelf());
        $this->rmaModelMock->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())->method('sendAuthorizeEmail');
        $this->statusHistoryMock->expects($this->once())
            ->method('saveSystemComment');

        $this->assertNull($this->controller->saveAction());
    }

    public function testCloseAction()
    {
        $entityId = 1;
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['entity_id', null, $entityId]
                    ]
                )
            );
        $this->rmaModelMock->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->will($this->returnSelf());
        $this->rmaModelMock->expects($this->once())
            ->method('canClose')
            ->will($this->returnValue(true));
        $this->rmaModelMock->expects($this->once())
            ->method('close')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())
            ->method('saveSystemComment');

        $this->assertNull($this->controller->closeAction());
    }


    public function testAddCommentAction()
    {
        $commentText = 'some comment';
        $visibleOnFront = true;
        $blockContents = [
            $commentText
        ];
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock('Magento\Framework\View\Element\BlockInterface', [], [], '', false);
        $coreHelperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);

        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->will(
                $this->returnValue(
                    [
                        'comment' => $commentText,
                        'is_visible_on_front' => $visibleOnFront,
                        'is_customer_notified' => true
                    ]
                )
            );
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($this->rmaModelMock));
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())
            ->method('setComment')
            ->with($commentText);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('comments_history')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($blockContents));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($coreHelperMock));
        $coreHelperMock->expects($this->once())
            ->method('jsonEncode')
            ->will($this->returnValue($commentText));

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with($commentText);

        $this->assertNull($this->controller->addCommentAction());
    }

    public function testLoadNewAttributesActionWithoutUserAttributes()
    {
        $productId = 1;
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());
        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnSelf());

        $this->responseMock->expects($this->never())
            ->method('setBody');

        $this->assertNull($this->controller->loadNewAttributesAction());
    }

    public function testLoadNewAttributesActionWithUserAttributes()
    {
        $productId = 1;
        $rmaMock = $this->getMock('Magento\Rma\Model\Item', [], [], '', false);
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock(
            'Magento\Framework\View\Element\Template',
            ['setProductId', 'initForm'],
            [],
            '',
            false
        );

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('product_id', null)
            ->will($this->returnValue($productId));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Rma\Model\Item', [])
            ->will($this->returnValue($rmaMock));
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));


        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('magento_rma_edit_item')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('setProductId')
            ->with($productId)
            ->will($this->returnSelf());

        $blockMock->expects($this->once())
            ->method('initForm')
            ->will($this->returnValue($this->formMock));

        $this->formMock->expects($this->once())
            ->method('hasNewAttributes')
            ->will($this->returnValue(true));
        $this->formMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue(['html', 'html', 'html']));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('Magento\Core\Helper\Data'))
            ->will($this->returnValue($this->helperMock));
        $this->helperMock->expects($this->once())
            ->method('jsonEncode')
            ->will($this->returnValue('response'));
        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('response'), $this->equalTo(false));
        $this->assertNull($this->controller->loadNewAttributesAction());
    }
}
