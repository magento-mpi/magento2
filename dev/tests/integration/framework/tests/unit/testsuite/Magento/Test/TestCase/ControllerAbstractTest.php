<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ControllerAbstractTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected $_bootstrap;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Message\Manager */
    private $messageManager;

    protected function setUp()
    {
        $this->messageManager = $this->getMock('\Magento\Message\Manager', array(), array(), '', false);
        $request = new \Magento\TestFramework\Request(
            $this->getMock('\Magento\App\Route\ConfigInterface', array(), array(), '', false),
            $this->getMock('Magento\App\Request\PathInfoProcessorInterface', array(), array(), '', false)
        );
        $response = new \Magento\TestFramework\Response(
            $this->getMock('\Magento\Stdlib\Cookie', array(), array(), '', false),
            $this->getMock('Magento\App\Http\Context', array(), array(), '', false)
        );

        $this->_objectManager = $this->getMock(
            'Magento\TestFramework\ObjectManager', array('get', 'create'), array(), '', false
        );
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('Magento\App\RequestInterface', $request),
                array('Magento\App\ResponseInterface', $response),
                array('Magento\Message\Manager', $this->messageManager),
            )));
    }

    /**
     * Bootstrap instance getter.
     * Mocking real bootstrap
     *
     * @return \Magento\TestFramework\Bootstrap
     */
    protected function _getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = $this->getMock(
                'Magento\TestFramework\Bootstrap', array('getAllOptions'), array(), '', false);
        }
        return $this->_bootstrap;
    }

    public function testGetRequest()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf('Magento\TestFramework\Request', $request);
    }

    public function testGetResponse()
    {
        $response = $this->getResponse();
        $this->assertInstanceOf('Magento\TestFramework\Response', $response);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAssert404NotFound()
    {
        $this->getRequest()->setControllerName('noroute');
        $this->getResponse()->setBody(
            '404 Not Found test <h3>We are sorry, but the page you are looking for cannot be found.</h3>'
        );
        $this->assert404NotFound();

        $this->getResponse()->setBody('');
        try {
            $this->assert404NotFound();
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }
        $this->fail('Failed response body validation');
    }

    /**
     * @expectedException \PHPUnit_Framework_AssertionFailedError
     */
    public function testAssertRedirectFailure()
    {
        $this->assertRedirect();
    }

    /**
     * @depends testAssertRedirectFailure
     */
    public function testAssertRedirect()
    {
        /*
         * Prevent calling \Magento\App\Response\Http::setRedirect() because it dispatches event,
         * which requires fully initialized application environment intentionally not available
         * for unit tests
         */
        $setRedirectMethod = new \ReflectionMethod('Zend_Controller_Response_Http', 'setRedirect');
        $setRedirectMethod->invoke($this->getResponse(), 'http://magentocommerce.com');
        $this->assertRedirect();
        $this->assertRedirect($this->equalTo('http://magentocommerce.com'));
    }

    /**
     * @param array $expectedMessages
     * @param string|null $messageTypeFilter
     * @dataProvider assertSessionMessagesDataProvider
     */
    public function testAssertSessionMessagesSuccess(array $expectedMessages, $messageTypeFilter)
    {
        $this->addSessionMessages();
        /** @var \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_Constraint $constraint */
        $constraint = $this->getMock('PHPUnit_Framework_Constraint', array('toString', 'matches'));
        $constraint
            ->expects($this->once())
            ->method('matches')
            ->with($expectedMessages)
            ->will($this->returnValue(true))
        ;
        $this->assertSessionMessages($constraint, $messageTypeFilter);
    }

    public function assertSessionMessagesDataProvider()
    {
        return array(
            'message waning type filtering' => array(
                array('some_warning'),
                \Magento\Message\MessageInterface::TYPE_WARNING
            ),
            'message error type filtering'    => array(
                array('error_one', 'error_two'),
                \Magento\Message\MessageInterface::TYPE_ERROR
            ),
            'message success type filtering'    => array(
                array('success!'),
                \Magento\Message\MessageInterface::TYPE_SUCCESS
            ),
        );
    }

    public function testAssertSessionMessagesAll()
    {
        $this->addSessionMessages();

        $this->assertSessionMessages($this->equalTo([
            'some_warning',
            'error_one',
            'error_two',
            'some_notice',
            'success!',
        ]));
    }

    public function testAssertSessionMessagesEmpty()
    {
        $messagesCollection =  new \Magento\Message\Collection();
        $this->messageManager->expects($this->any())->method('getMessages')
            ->will($this->returnValue($messagesCollection));

        $this->assertSessionMessages($this->isEmpty());
    }

    private function addSessionMessages()
    {
        // emulate session messages
        $messagesCollection = new \Magento\Message\Collection();
        $messagesCollection
            ->addMessage(new \Magento\Message\Warning('some_warning'))
            ->addMessage(new \Magento\Message\Error('error_one'))
            ->addMessage(new \Magento\Message\Error('error_two'))
            ->addMessage(new \Magento\Message\Notice('some_notice'))
            ->addMessage(new \Magento\Message\Success('success!'))
        ;
        $this->messageManager->expects($this->any())->method('getMessages')
            ->will($this->returnValue($messagesCollection));
    }
}
