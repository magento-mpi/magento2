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

abstract class Magento_Test_Webservice_Rest_Abstract extends Magento_Test_Webservice
{
    /**
     * REST Webservice adapter instances registry
     *
     * @var array
     */
    protected static $_adapterRegistry = array();

    /**
     * REST Webservice user type (admin/customer/guest)
     *
     * @var string|null
     */
    protected $_userType;

    /**
     * Get adapter instance
     *
     * @return Magento_Test_Webservice_Rest_Adapter
     */
    protected function getInstance()
    {
        $instance = null;
        if (isset(self::$_adapterRegistry[$this->_userType])){
            $instance = self::$_adapterRegistry[$this->_userType];
        }

        return $instance;
    }

    /**
     * Set adapter instance
     *
     * @param Magento_Test_Webservice_Rest_Adapter $instance
     */
    protected function setInstance(Magento_Test_Webservice_Rest_Adapter $instance)
    {
        self::$_adapterRegistry[$this->_userType] = $instance;
    }

    /**
     * Get webservice adapter
     *
     * @param array $options
     * @return Magento_Test_Webservice_Rest_Adapter
     */
    public function getWebService($options = null)
    {
        if (null === $this->getInstance()) {
            $this->setInstance(new Magento_Test_Webservice_Rest_Adapter());
            $options['type'] = $this->_userType;
            $this->getInstance()->init($options);
        }

        return $this->getInstance();
    }

    /**
     * REST GET
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callGet($resourceName, $params = array())
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callGet($resourceName, $params);
    }

    /**
     * REST DELETE
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callDelete($resourceName, $params = array())
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callDelete($resourceName, $params);
    }

    /**
     * REST POST
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPost($resourceName, $params)
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callPost($resourceName, $params);
    }

    /**
     * REST PUT
     *
     * @param string $resourceName
     * @param array $params
     * @return Magento_Test_Webservice_Rest_ResponseDecorator
     */
    public function callPut($resourceName, $params)
    {
        if (null === $this->getInstance()) {
            $this->getWebService();
        }

        return $this->getInstance()->callPut($resourceName, $params);
    }

    /**
     * Check error messages either by pattern if set or for exact match
     *
     * @param array $testData format: array('messages' => array())
     * <br /><b>or</b> array('message_patterns' => array('pattern' => $pattern, 'matches_count' => $count))
     * @param array $errorMessages
     * @throws LogicException
     */
    protected function _checkErrorMessages($testData, $errorMessages)
    {
        if (isset($testData['message_patterns'])) {
            $this->_checkErrorMessagesByPattern($testData['message_patterns'], $errorMessages);
        } else if (isset($testData['messages'])) {
            $this->assertMessagesEqual($testData['messages'], $errorMessages);
        } else {
            throw new LogicException("Data set seems to be invalid as no error messages checks are performed");
        }
    }

    /**
     * Check error messages using regular expression pattern
     *
     * @param array $patterns format: array('pattern' => $pattern, 'matches_count' => $count)
     * @param array $errorMessages
     * @throws LogicException
     */
    protected function _checkErrorMessagesByPattern($patterns, $errorMessages)
    {
        if (!is_array($patterns)) {
            throw new LogicException("Patterns parameter must be be an array");
        }
        if (!is_array($errorMessages)) {
            throw new LogicException("Error messages parameter must be an array");
        }
        foreach ($patterns as $messagePattern) {
            if (!isset($messagePattern['pattern']) || !isset($messagePattern['matches_count'])) {
                throw new LogicException("Each pattern must contain 'pattern' and 'matches_count' fields");
            }
        }
        $messagesMatchingToPatterns = array();
        foreach ($errorMessages as $message) {
            $isMessageValid = false;
            foreach ($patterns as $messagePattern) {
                if (preg_match($messagePattern['pattern'], $message)) {
                    $messagesMatchingToPatterns[$messagePattern['pattern']][] = $message;
                    $isMessageValid = true;
                    break;
                }
            }
            $this->assertTrue($isMessageValid, "Received message does not match any pattern: '$message'");
        }
        // check if correct messages quantity corresponds to each of patterns
        foreach ($patterns as $messagePattern) {
            $actualMatchingMessagesCount = isset($messagesMatchingToPatterns[$messagePattern['pattern']])
                ? count($messagesMatchingToPatterns[$messagePattern['pattern']]) : 0;
            $this->assertEquals($messagePattern['matches_count'], $actualMatchingMessagesCount,
                "Invalid error messages quantity received for pattern: '{$messagePattern['pattern']}'");
        }
    }

    /**
     * Check if expected error messages are set in rest response
     *
     * @param Magento_Test_Webservice_Rest_ResponseDecorator $restResponse
     * @param array|string $expectedMessages
     * @param string $expectedCode
     */
    protected function _checkErrorMessagesInResponse($restResponse, $expectedMessages, $expectedCode = null)
    {
        $expectedMessages = is_array($expectedMessages) ? $expectedMessages : array($expectedMessages);
        $expectedCode = $expectedCode ? $expectedCode : Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $this->assertEquals($expectedCode, $restResponse->getStatus(),
            "Invalid response code");
        $body = $restResponse->getBody();
        $this->assertTrue(isset($body['messages']['error']), "Error messages expected to be set");
        $receivedMessages = array();
        foreach($body['messages']['error'] as $error) {
            $receivedMessages[] = $error['message'];
        }
        $this->assertMessagesEqual($expectedMessages, $receivedMessages);
    }

    /**
     * Check if expected success messages are set in rest response
     *
     * @param Magento_Test_Webservice_Rest_ResponseDecorator $restResponse
     * @param array|string $expectedMessages
     */
    protected function _checkSuccessMessagesInResponse($restResponse, $expectedMessages)
    {
        $expectedMessages = is_array($expectedMessages) ? $expectedMessages : array($expectedMessages);
        $body = $restResponse->getBody();
        $this->assertTrue(isset($body['messages']['success']), "Success messages expected to be set");
        $receivedMessages = array();
        foreach($body['messages']['success'] as $success) {
            $receivedMessages[] = $success['message'];
        }
        $this->assertMessagesEqual($expectedMessages, $receivedMessages);
    }
}
