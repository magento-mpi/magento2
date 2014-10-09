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
abstract class RmaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected $action;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $responseMock;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistryMock;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManagerMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManagerMock;

    /**
     * @var \Magento\Backend\Model\Session
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
     * @var \Magento\Rma\Model\Rma\RmaDataMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMapperMock;


    /**
     * test setUp
     */
    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $contextMock = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $backendHelperMock = $this->getMock('Magento\Backend\Helper\Data', [], [], '', false);
        $this->rmaDataMapperMock = $this->getMock('Magento\Rma\Model\Rma\RmaDataMapper', [], [], '', false);
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

        $this->action = $objectManager->getObject(
            '\\Magento\\Rma\\Controller\\Adminhtml\\Rma\\' . $this->name,
            [
                'context' => $contextMock,
                'coreRegistry' => $this->coreRegistryMock,
                'rmaDataMapper' => $this->rmaDataMapperMock
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

    protected function initRequestData($commentText = '', $visibleOnFront = true)
    {
        $rmaConfirmation = true;
        $post = [
            'items' => [],
            'rma_confirmation' => $rmaConfirmation,
            'comment' => [
                'comment' => $commentText,
                'is_visible_on_front' => $visibleOnFront,
            ]
        ];
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
        return $post;
    }
}
