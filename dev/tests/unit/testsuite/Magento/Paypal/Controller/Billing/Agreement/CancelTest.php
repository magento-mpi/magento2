<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Billing\Agreement;

class CancelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Paypal\Controller\Billing\Agreement
     */
    protected $_controller;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registry;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_messageManager;

    /**
     * @var \Magento\Paypal\Model\Billing\Agreement|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_agreement;

    protected function setUp()
    {
        $this->_session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);

        $this->_agreement = $this->getMock(
            'Magento\Paypal\Model\Billing\Agreement',
            array('load', 'getId', 'getCustomerId', 'getReferenceId', 'canCancel', 'cancel', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_agreement->expects($this->once())->method('load')->with(15)->will($this->returnSelf());
        $this->_agreement->expects($this->once())->method('getId')->will($this->returnValue(15));
        $this->_agreement->expects($this->once())->method('getCustomerId')->will($this->returnValue(871));

        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManager');
        $this->_objectManager->expects(
            $this->atLeastOnce()
        )->method(
            'get'
        )->will(
            $this->returnValueMap(array(array('Magento\Customer\Model\Session', $this->_session)))
        );
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Paypal\Model\Billing\Agreement'
        )->will(
            $this->returnValue($this->_agreement)
        );

        $this->_request = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->_request->expects($this->once())->method('getParam')->with('agreement')->will($this->returnValue(15));

        $response = $this->getMock('Magento\Framework\App\ResponseInterface');

        $redirect = $this->getMock('Magento\Framework\App\Response\RedirectInterface');

        $this->_messageManager = $this->getMock('Magento\Framework\Message\ManagerInterface');

        $context = $this->getMock('Magento\Framework\App\Action\Context', array(), array(), '', false);
        $context->expects($this->any())->method('getObjectManager')->will($this->returnValue($this->_objectManager));
        $context->expects($this->any())->method('getRequest')->will($this->returnValue($this->_request));
        $context->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $context->expects($this->any())->method('getRedirect')->will($this->returnValue($redirect));
        $context->expects($this->any())->method('getMessageManager')->will($this->returnValue($this->_messageManager));

        $this->_registry = $this->getMock('Magento\Framework\Registry', array(), array(), '', false);

        $title = $this->getMock('Magento\Framework\App\Action\Title', array(), array(), '', false);

        $this->_controller = new Cancel($context, $this->_registry, $title);
    }

    public function testExecuteActionSuccess()
    {
        $this->_agreement->expects($this->once())->method('getReferenceId')->will($this->returnValue('r15'));
        $this->_agreement->expects($this->once())->method('canCancel')->will($this->returnValue(true));
        $this->_agreement->expects($this->once())->method('cancel');

        $noticeMessage = 'The billing agreement "r15" has been canceled.';
        $this->_session->expects($this->once())->method('getCustomerId')->will($this->returnValue(871));
        $this->_messageManager->expects($this->once())->method('addNotice')->with($noticeMessage);
        $this->_messageManager->expects($this->never())->method('addError');

        $this->_registry->expects(
            $this->once()
        )->method(
            'register'
        )->with(
            'current_billing_agreement',
            $this->identicalTo($this->_agreement)
        );

        $this->_controller->execute();
    }

    public function testExecuteAgreementDoesNotBelongToCustomer()
    {
        $this->_agreement->expects($this->never())->method('canCancel');
        $this->_agreement->expects($this->never())->method('cancel');

        $errorMessage = 'Please specify the correct billing agreement ID and try again.';
        $this->_session->expects($this->once())->method('getCustomerId')->will($this->returnValue(938));
        $this->_messageManager->expects($this->once())->method('addError')->with($errorMessage);

        $this->_registry->expects($this->never())->method('register');

        $this->_controller->execute();
    }

    public function testExecuteAgreementStatusDoesNotAllowToCancel()
    {
        $this->_agreement->expects($this->once())->method('canCancel')->will($this->returnValue(false));
        $this->_agreement->expects($this->never())->method('cancel');

        $this->_session->expects($this->once())->method('getCustomerId')->will($this->returnValue(871));
        $this->_messageManager->expects($this->never())->method('addNotice');
        $this->_messageManager->expects($this->never())->method('addError');

        $this->_registry->expects(
            $this->once()
        )->method(
            'register'
        )->with(
            'current_billing_agreement',
            $this->identicalTo($this->_agreement)
        );

        $this->_controller->execute();
    }
}
