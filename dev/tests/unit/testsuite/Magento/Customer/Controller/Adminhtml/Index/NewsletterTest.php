<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;
use Magento\Framework\App\ObjectManager;

/**
 * Unit test for \Magento\Customer\Controller\Adminhtml\Index controller
 */
class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Request mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Response mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * Instance of mocked tested object
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Controller\Adminhtml\Index
     */
    protected $_testedObject;

    /**
     * ObjectManager mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_acctServiceMock;

    /**
     * Session mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * Backend helper mock instance
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Prepare required values
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $objectManagerMock = $this->getMock('Magento\Framework\App\ObjectManager', [], [], '', false);
        ObjectManager::setInstance($objectManagerMock);

        $this->_request = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_response = $this->getMockBuilder(
            'Magento\Framework\App\Response\Http'
        )->disableOriginalConstructor()->setMethods(
                array('setRedirect', 'getHeader')
            )->getMock();

        $this->_response->expects(
            $this->any()
        )->method(
                'getHeader'
            )->with(
                $this->equalTo('X-Frame-Options')
            )->will(
                $this->returnValue(true)
            );

        $this->_objectManager = $this->getMockBuilder(
            'Magento\Framework\App\ObjectManager'
        )->disableOriginalConstructor()->setMethods(
                array('get', 'create')
            )->getMock();
        $frontControllerMock = $this->getMockBuilder(
            'Magento\Framework\App\FrontController'
        )->disableOriginalConstructor()->getMock();

        $actionFlagMock = $this->getMockBuilder('Magento\Framework\App\ActionFlag')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_session = $this->getMockBuilder(
            'Magento\Backend\Model\Session'
        )->disableOriginalConstructor()->setMethods(
                array('setIsUrlNotice', '__wakeup')
            )->getMock();
        $this->_session->expects($this->any())->method('setIsUrlNotice');

        $this->_helper = $this->getMockBuilder(
            'Magento\Backend\Helper\Data'
        )->disableOriginalConstructor()->setMethods(
                array('getUrl')
            )->getMock();

        $this->messageManager = $this->getMockBuilder(
            'Magento\Framework\Message\Manager'
        )->disableOriginalConstructor()->setMethods(
                array('addSuccess', 'addMessage', 'addException')
            )->getMock();

        $contextArgs = array(
            'getHelper',
            'getSession',
            'getAuthorization',
            'getTranslator',
            'getObjectManager',
            'getFrontController',
            'getActionFlag',
            'getMessageManager',
            'getLayoutFactory',
            'getEventManager',
            'getRequest',
            'getResponse',
            'getTitle',
            'getView'
        );
        $contextMock = $this->getMockBuilder(
            '\Magento\Backend\App\Action\Context'
        )->disableOriginalConstructor()->setMethods(
                $contextArgs
            )->getMock();
        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->_request));
        $contextMock->expects($this->any())->method('getResponse')->will($this->returnValue($this->_response));
        $contextMock->expects(
            $this->any()
        )->method(
                'getObjectManager'
            )->will(
                $this->returnValue($this->_objectManager)
            );
        $contextMock->expects(
            $this->any()
        )->method(
                'getFrontController'
            )->will(
                $this->returnValue($frontControllerMock)
            );
        $contextMock->expects($this->any())->method('getActionFlag')->will($this->returnValue($actionFlagMock));

        $contextMock->expects($this->any())->method('getHelper')->will($this->returnValue($this->_helper));
        $contextMock->expects($this->any())->method('getSession')->will($this->returnValue($this->_session));
        $contextMock->expects(
            $this->any()
        )->method(
                'getMessageManager'
            )->will(
                $this->returnValue($this->messageManager)
            );
        $titleMock =  $this->getMockBuilder('\Magento\Framework\App\Action\Title')->getMock();
        $contextMock->expects($this->any())->method('getTitle')->will($this->returnValue($titleMock));
        $viewMock =  $this->getMockBuilder('\Magento\Framework\App\ViewInterface')->getMock();
        $viewMock->expects($this->any())->method('loadLayout')->will($this->returnSelf());
        $contextMock->expects($this->any())->method('getView')->will($this->returnValue($viewMock));

        $this->_acctServiceMock = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        )->getMock();

        $args = array('context' => $contextMock, 'accountService' => $this->_acctServiceMock);



        $helperObjectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_testedObject = $helperObjectManager->getObject(
            'Magento\Customer\Controller\Adminhtml\Index\Newsletter',
            $args
        );
    }

    public function testNewsletterAction()
    {
        $subscriberMock = $this->getMock(
            '\Magento\Newsletter\Model\Subscriber',
            array(),
            array(),
            '',
            false
        );
        $subscriberMock->expects($this->once())->method('loadByCustomerId');
        $this->_objectManager
            ->expects($this->at(1))
            ->method('create')
            ->with('Magento\Newsletter\Model\Subscriber')
            ->will($this->returnValue($subscriberMock));
        $this->_testedObject->execute();
    }
}
