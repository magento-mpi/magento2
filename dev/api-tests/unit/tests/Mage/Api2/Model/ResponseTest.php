<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 response model
 */
class Mage_Api2_Model_ResponseTest extends Mage_PHPUnit_TestCase
{
    protected $_messageFixture = array(
        'message' => 'test message',
        'code'    => Mage_Api2_Model_Server::HTTP_OK,
        'param1'  => 'param1',
        'param2'  => 'param2'
    );

    /**
     * Test add message
     */
    public function testAddMessage()
    {
        /* @var $response Mage_Api2_Model_Response */
        $response = Mage::getModel('api2/response');

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );
        $this->assertEquals($fluent, $response);

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_ERROR
        );
        $this->assertEquals($fluent, $response);

        $fluent = $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_WARNING
        );
        $this->assertEquals($fluent, $response);

        $messages = $response->getMessages();
        $this->assertEquals($this->_messageFixture, $messages[Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS][0]);
        $this->assertEquals($this->_messageFixture, $messages[Mage_Api2_Model_Response::MESSAGE_TYPE_ERROR][0]);
        $this->assertEquals($this->_messageFixture, $messages[Mage_Api2_Model_Response::MESSAGE_TYPE_WARNING][0]);
    }

    /**
     * Test has message
     */
    public function testHasMessage()
    {
        /* @var $response Mage_Api2_Model_Response */
        $response = Mage::getModel('api2/response');
        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_WARNING
        );
        $this->assertEquals($response->hasMessages(), true);
    }

    /**
     * Test clear messages
     */
    public function testClearMessages()
    {
        /* @var $response Mage_Api2_Model_Response */
        $response = Mage::getModel('api2/response');

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS
        );

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_ERROR
        );

        $response->addMessage(
            $this->_messageFixture['message'],
            $this->_messageFixture['code'],
            array('param1' => $this->_messageFixture['param1'], 'param2' => $this->_messageFixture['param2']),
            Mage_Api2_Model_Response::MESSAGE_TYPE_WARNING
        );

        $response->clearMessages();

        $this->assertEquals($response->getMessages(), array());
    }
}
