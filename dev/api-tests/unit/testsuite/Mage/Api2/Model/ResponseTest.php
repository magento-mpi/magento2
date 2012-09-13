<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Webapi response model
 */
class Mage_Webapi_Model_ResponseTest extends Mage_PHPUnit_TestCase
{
    protected $_messageFixture = array(
        'message' => 'test message',
        'code'    => Mage_Webapi_Controller_Front_Rest::HTTP_OK,
        'param1'  => 'param1',
        'param2'  => 'param2'
    );

    /**
     * Test add message
     */
    public function testAddMessage()
    {
        /* @var $response Mage_Webapi_Model_Response */
        $response = Mage::getModel('Mage_Webapi_Model_Response');

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_SUCCESS
        );
        $this->assertEquals($fluent, $response);

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_ERROR
        );
        $this->assertEquals($fluent, $response);

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_WARNING
        );
        $this->assertEquals($fluent, $response);

        $messages = $response->getMessages();
        $this->assertEquals($this->_messageFixture, $messages[Mage_Webapi_Model_Response::MESSAGE_TYPE_SUCCESS][0]);
        $this->assertEquals($this->_messageFixture, $messages[Mage_Webapi_Model_Response::MESSAGE_TYPE_ERROR][0]);
        $this->assertEquals($this->_messageFixture, $messages[Mage_Webapi_Model_Response::MESSAGE_TYPE_WARNING][0]);
    }

    /**
     * Test has message
     */
    public function testHasMessage()
    {
        /* @var $response Mage_Webapi_Model_Response */
        $response = Mage::getModel('Mage_Webapi_Model_Response');
        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_WARNING
        );
        $this->assertEquals($response->hasMessages(), true);
    }

    /**
     * Test clear messages
     */
    public function testClearMessages()
    {
        /* @var $response Mage_Webapi_Model_Response */
        $response = Mage::getModel('Mage_Webapi_Model_Response');

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_SUCCESS
        );

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_ERROR
        );

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Webapi_Model_Response::MESSAGE_TYPE_WARNING
        );

        $response->clearMessages();

        $this->assertEquals($response->getMessages(), array());
    }
}
