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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 /**
 * MessageHandler model test
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Api Team <api-team@ebay.com>
 */
class Mage_Api_Model_Server_MessageHandlerTest extends Mage_PHPUnit_TestCase
{
    /**
     * Model options
     *
     * @var array
     */
    protected $_options;

    /**
     * Get test options for MessageHandler
     *
     * @return array
     */
    protected function _getOptions()
    {
        if (null === $this->_options) {
            $this->_options = require dirname(__FILE__) . '/_fixture/messageHandlerData.php';
        }
        return $this->_options;
    }

    /**
     * Test failing construct model
     */
    public function testFailConstruct()
    {
        try {
            Mage::getModel('api/server_messageHandler');
            $this->fail('Model "api/server_messageHandler" must not construct.');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($e);
            $this->assertTrue($class == 'Exception', sprintf('Exception class "%s" is not excepted', $class));
        }
    }

    /**
     * Test failing add incorrect message with not exist unknown_error
     */
    public function testNoExistUnknownError()
    {
        try {
            /** @var $messageModel Mage_Api_Model_Server_MessageHandler */
            $options = $this->_getOptions();
            unset($options['messages']['core']['internal_error']['unknown_error']);
            $messageModel = Mage::getModel('api/server_messageHandler', $options);
            $messageModel->addErrorMessage('some_message', null, false);
            $this->fail('Model must not run method "addErrorMessage".');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            throw $e;
        } catch (Exception $e) {
            $class = get_class($e);
            $this->assertEquals('Exception', $class,
                sprintf('Exception class "%s" is not excepted', $class));
            $this->assertEquals('3', $e->getCode(),
                sprintf('Exception code "%s" is not excepted', $e->getCode()));
        }
    }

    /**
     * Test output as XML
     */
    public function testXmlOutput()
    {
        $options = $this->_getOptions();
        /** @var $messageModel Mage_Api_Model_Server_MessageHandler */
        $messageModel = Mage::getModel('api/server_messageHandler', $options);
        //add messages
        $messageModel->addErrorMessage('webmany_payment/email_invalid', null, false);
        $messageModel->addErrorMessage('email_invalid', null, false);
        $customPasswordMessage = 'Password is not confirmed.';
        $messageModel->addErrorMessage('password_confirm', $customPasswordMessage, false);
        $messageModel->addErrorMessage('coooode_invalid', null, false); //invalid code error
        $xml = $messageModel->getResponseMessages(Mage_Api_Model_Server_MessageHandler::OUTPUT_XML);
        //check domains
        /** @var $items SimpleXMLIterator */
        $items = $xml->xpath('error/domain');
        $this->assertEquals('webmany_payment:validation', (string) $items[0]);
        $this->assertEquals('my_module:validation', (string) $items[1]);
        $this->assertEquals('core:validation', (string) $items[2]);
        $this->assertEquals('core:internal_error', (string) $items[3]);
        $this->assertEquals(4, count($items));
         //check codes
        $items = $xml->xpath('error/code');
        $this->assertEquals('email_invalid', (string) $items[0]);
        $this->assertEquals('email_invalid', (string) $items[1]);
        $this->assertEquals('password_confirm', (string) $items[2]);
        $this->assertEquals('unknown_error', (string) $items[3]);
        $this->assertEquals(4, count($items));
         //check messages
        $items = $xml->xpath('error/message');
        $this->assertEquals(
            $options['messages']['webmany_payment']['validation']['email_invalid'],
            (string) $items[0]);
        $this->assertEquals(
            $options['messages']['my_module']['validation']['email_invalid'],
            (string) $items[1]);
        $this->assertEquals(
            $customPasswordMessage,
            (string) $items[2]);
        $this->assertEquals(
            $options['messages']['core']['internal_error']['unknown_error'],
            (string) $items[3]);
        $this->assertEquals(4, count($items));
         //bigger number code must be higher
        $this->assertEquals(
            $options['domains']['internal_error']['http_code'],
            $messageModel->getHttpCode());
    }

    /**
     * Test output as array
     */
    public function testArrayOutput()
    {
        $options = $this->_getOptions();
        /** @var $messageModel Mage_Api_Model_Server_MessageHandler */
        $messageModel = Mage::getModel('api/server_messageHandler', $options);
        //add messages
        $messageModel->addNotificationMessage('webmany_payment/ok');
        $messageModel->addNotificationMessage('processing');
        $array = $messageModel->getResponseMessages(Mage_Api_Model_Server_MessageHandler::OUTPUT_ARRAY);
        $this->assertEquals(
            array(
                'type' => 'notification',
                'code' => 'ok',
                'message' => 'WebMoney request is done.',
                'domain' => 'webmany_payment:success'
            ),
            $array[0]
        );
        $this->assertEquals(
            array(
                'type' => 'notification',
                'code' => 'processing',
                'message' => 'Request in processing.',
                'domain' => 'core:processing'
            ),
            $array[1]
        );
        //bigger number code must be higher
        $httpCode = $messageModel->getHttpCode();
        $this->assertEquals(
            $options['domains']['processing']['http_code'],
            $httpCode,
            sprintf('HTTP code "%s" is not expected', $httpCode)
        );
    }

    /**
     * Test output as array
     */
    public function testOutputWithoutMessagesText()
    {
        $options = $this->_getOptions();
        $options['useTextMessages'] = false;
        /** @var $messageModel Mage_Api_Model_Server_MessageHandler */
        $messageModel = Mage::getModel('api/server_messageHandler', $options);
        //add messages
        $messageModel->addNotificationMessage('webmany_payment/ok');
         $array = $messageModel->getResponseMessages(Mage_Api_Model_Server_MessageHandler::OUTPUT_ARRAY);
         $this->assertEquals(
            array(
                'type' => 'notification',
                'code' => 'ok',
                'domain' => 'webmany_payment:success'
            ),
            $array[0]
        );
    }
}
