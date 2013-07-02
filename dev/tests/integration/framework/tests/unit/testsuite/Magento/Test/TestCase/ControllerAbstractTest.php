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

class Magento_Test_TestCase_ControllerAbstractTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected $_bootstrap;

    protected function setUp()
    {
        if (!Mage::getObjectManager()) {
            $instanceConfig = new Magento_Test_ObjectManager_Config();
            $factory = new Magento_ObjectManager_Interception_FactoryDecorator(
                new Magento_ObjectManager_Factory_Factory($instanceConfig),
                $instanceConfig
            );

            Mage::setObjectManager(

                new Magento_Test_ObjectManager(
                    $factory,
                    $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false)
                )
            );
        }
        parent::setUp();

        // emulate session messages
        $messagesCollection = new Mage_Core_Model_Message_Collection();
        $messagesCollection
            ->add(new Mage_Core_Model_Message_Warning('some_warning'))
            ->add(new Mage_Core_Model_Message_Error('error_one'))
            ->add(new Mage_Core_Model_Message_Error('error_two'))
            ->add(new Mage_Core_Model_Message_Notice('some_notice'))
        ;
        $sessionModelFixture = new Varien_Object(array('messages' => $messagesCollection));
        $this->_objectManager->addSharedInstance($sessionModelFixture, 'Mage_Core_Model_Session');
    }

    /**
     * Bootstrap instance getter.
     * Mocking real bootstrap
     *
     * @return Magento_Test_Bootstrap
     */
    protected function _getBootstrap()
    {
        if (!$this->_bootstrap) {
            $this->_bootstrap = $this->getMock('Magento_Test_Bootstrap', array('getAllOptions'), array(), '', false);
        }
        return $this->_bootstrap;
    }

    public function testGetRequest()
    {
        $this->_objectManager = $this->getMock('Magento_Test_ObjectManager', array(), array(), '', false);
        $request = $this->getRequest();
        $this->assertInstanceOf('Magento_Test_Request', $request);
        $this->assertSame($request, $this->getRequest());
    }

    public function testGetResponse()
    {
        $this->_objectManager = $this->getMock('Magento_Test_ObjectManager', array(), array(), '', false);
        $response = $this->getResponse();
        $this->assertInstanceOf('Magento_Test_Response', $response);
        $this->assertSame($response, $this->getResponse());
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testAssert404NotFound()
    {
        $this->_objectManager = $this->getMock('Magento_Test_ObjectManager', array(), array(), '', false);
        $this->getRequest()->setActionName('noRoute');
        $this->getResponse()->setBody(
            '404 Not Found test <h3>We are sorry, but the page you are looking for cannot be found.</h3>'
        );
        $this->assert404NotFound();

        $this->getResponse()->setBody('');
        try {
            $this->assert404NotFound();
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            return;
        }
        $this->fail('Failed response body validation');
    }

    /**
     * @expectedException PHPUnit_Framework_AssertionFailedError
     */
    public function testAssertRedirectFailure()
    {
        $this->_objectManager = $this->getMock('Magento_Test_ObjectManager', array(), array(), '', false);
        $this->assertRedirect();
    }

    /**
     * @depends testAssertRedirectFailure
     */
    public function testAssertRedirect()
    {
        $this->_objectManager = $this->getMock('Magento_Test_ObjectManager', array(), array(), '', false);
        /*
         * Prevent calling Mage_Core_Controller_Response_Http::setRedirect() because it executes Mage::dispatchEvent(),
         * which requires fully initialized application environment intentionally not available for unit tests
         */
        $setRedirectMethod = new ReflectionMethod('Zend_Controller_Response_Http', 'setRedirect');
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
            'message type filtering'    => array(array('error_one', 'error_two'), Mage_Core_Model_Message::ERROR),
        );
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Session messages do not meet expectations
     */
    public function testAssertSessionMessagesFailure()
    {
        $this->assertSessionMessages($this->isEmpty());
    }
}
