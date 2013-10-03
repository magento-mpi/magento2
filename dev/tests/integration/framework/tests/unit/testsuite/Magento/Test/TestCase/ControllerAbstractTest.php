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

    protected function setUp()
    {
        // emulate session messages
        $messagesCollection = new \Magento\Core\Model\Message\Collection();
        $messagesCollection
            ->add(new \Magento\Core\Model\Message\Warning('some_warning'))
            ->add(new \Magento\Core\Model\Message\Error('error_one'))
            ->add(new \Magento\Core\Model\Message\Error('error_two'))
            ->add(new \Magento\Core\Model\Message\Notice('some_notice'))
        ;
        $session = new \Magento\Object(array('messages' => $messagesCollection));
        $request = new \Magento\TestFramework\Request(
            $this->getMock('Magento\Core\Model\StoreManager', [], [], '', false),
            $this->getMock('Magento\Backend\Helper\Data', [], [], '', false)
        );
        $response = new \Magento\TestFramework\Response(
            $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false)
        );

        $this->_objectManager = $this->getMock(
            'Magento\TestFramework\ObjectManager', array('get', 'create'), array(), '', false
        );
        $this->_objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('Magento\TestFramework\Request', $request),
                array('Magento\TestFramework\Response', $response),
                array('Magento\Core\Model\Session', $session),
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
        $this->getRequest()->setActionName('noRoute');
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
         * Prevent calling \Magento\Core\Controller\Response\Http::setRedirect() because it dispatches event,
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
            'no message type filtering' => array(array('some_warning', 'error_one', 'error_two', 'some_notice'), null),
            'message type filtering'    => array(array('error_one', 'error_two'), \Magento\Core\Model\Message::ERROR),
        );
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Session messages do not meet expectations
     */
    public function testAssertSessionMessagesFailure()
    {
        $this->assertSessionMessages($this->isEmpty());
    }
}
