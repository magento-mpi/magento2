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
 * WebServices default server message handler
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento API Team <apia-team@ebay.com>
 */
class Mage_Api_Model_Server_MessageHandler
{
    /**#@+
     * Message types
     *
     * @var string
     */
    const TYPE_ERROR        = 'error';
    const TYPE_NOTIFICATION = 'notification';
    /**#@-*/

    /**#@+
     * Output response message types
     *
     * @var string
     */
    const OUTPUT_ARRAY        = 'array';
    const OUTPUT_XML          = 'xml';
    /**#@-*/

    /**
     * Messages list
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Message domains list
     *
     * @var array
     */
    protected $_domains = array();

    /**
     * Messages to response
     *
     * @var array
     */
    protected $_responseMessages = array();

    /**
     * Response HTTP code
     *
     * Default 200 OK
     *
     * @var int
     */
    protected $_httpCode = 200;

    /**
     * Module name of resource
     *
     * @var array
     */
    protected $_moduleName;

    /**
     * Module name of resource
     *
     * @var array
     */
    protected $_defaultModuleName = 'core';

    /**
     * Unknown message error code
     *
     * @var array
     */
    protected $_unknownCodeError = 'unknown_error';

    /**
     * Flag of using text messages in the output
     *
     * @var bool
     */
    protected $_useTextMessages = true;

    /**
     * Prepare configuration
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if (empty($options)) {
            throw new Exception('Options is empty.', 1);
        }
        if (empty($options['module'])) {
            throw new Exception('Module name is empty.', 1);
        }
        if (empty($options['domains'])) {
            throw new Exception('Message domains is empty.', 1);
        }
        if (empty($options['messages'])) {
            throw new Exception('Messages is empty.', 1);
        }

        $this->_moduleName = $options['module'];

        $this->_domains = $options['domains'];

        $this->_messages = array();
        foreach ($options['messages'] as $module => $list) {
            foreach ($list as $domain => $messages) {
                if (!isset($this->_domains[$domain])) {
                    continue;
                }
                foreach ($messages as $code => $message) {
                    $type = $this->_domains[$domain]['type'];
                    $this->_messages[$type][$module . '/' . $code] = array(
                        'message' => $message,
                        'domain' => $domain
                    );
                }
            }
        }

        if (isset($options['useTextMessages'])) {
            $this->_useTextMessages = (bool) $options['useTextMessages'];
        }
    }

    /**
     * Add message
     *
     * Code can be simple only code message, or with module prefix
     * email_invalid or my_module/email_invalid
     *
     * @param string $code
     * @param string $module
     * @param string $customMessage     If not empty then it overwrite exist message
     * @param string $type              Message type (error, notification)
     * @param bool $exception           Throw exception when added exception on added
     * @return Mage_Api_Model_Server_MessageHandler
     * @throws Mage_Api_Model_Server_MessageHandler_Exception
     */
    protected function _addMessage($code, $module, $customMessage, $type, $exception = false)
    {
        $path = $module . '/' . $code;
        if (!isset($this->_messages[$type][$path])) {
            throw new Exception(sprintf('API message path "%s" not found.', $path), 3);
        }
        $message = ($customMessage ? $customMessage : $this->_messages[$type][$path]['message']);
        $domain = $this->_messages[$type][$path]['domain'];
        $arr = array(
            'type' => $type,
            'code' => $code,
            'domain' => $module . ':' . $domain,
    );
        if ($this->_useTextMessages) {
            $arr['message'] = $message;
        }
        $this->_responseMessages[] = $arr;

        $this->setHttpCode($this->_domains[$domain]['http_code']);

        if ($exception) {
            throw new Mage_Api_Model_Server_MessageHandler_Exception($message);
        }
        return $this;
    }

    /**
     * Add error message
     *
     * @param $code
     * @param string $customMessage
     * @param bool $exception
     * @return Mage_Api_Model_Server_MessageHandler
     * @throws Mage_Api_Model_Server_MessageHandler_Exception
     */
    public function addErrorMessage($code, $customMessage = '', $exception = true)
    {
        if (false === strpos($code, '/')) {
            $module = $this->_moduleName;
            if (!isset($this->_messages[self::TYPE_ERROR][$module . '/' . $code])) {
                $module = $this->_defaultModuleName;
            }
            $path = $module . '/' . $code;
        } else {
            $path = $code;
            list($module, $code) = explode('/', $code);
        }

        if (!isset($this->_messages[self::TYPE_ERROR][$path])) {
            $module = $this->_defaultModuleName;
            $code = $this->_unknownCodeError;
            $customMessage = '';
        }

        $this->_addMessage($code, $module, $customMessage, self::TYPE_ERROR, $exception);

        return $this;
    }

    /**
     * Add success message
     *
     * @param $code
     * @param string $customMessage
     * @return Mage_Api_Model_Server_MessageHandler
     * @throws Mage_Api_Model_Server_MessageHandler_Exception
     */
    public function addNotificationMessage($code, $customMessage = '')
    {
        if (false === strpos($code, '/')) {
            $module = $this->_moduleName;
            if (!isset($this->_messages[self::TYPE_NOTIFICATION][$module . '/' . $code])) {
                $module = $this->_defaultModuleName;
            }
            $path = $module . '/' . $code;
        } else {
            $path = $code;
            list($module, $code) = explode('/', $code);
        }

        if (!isset($this->_messages[self::TYPE_NOTIFICATION][$path])) {
            //notification not found, add unknown error with exception
            $this->_addMessage(
                $this->_unknownCodeError,
                $this->_defaultModuleName,
                '', self::TYPE_ERROR, true);
        }

        $this->_addMessage($code, $module, $customMessage, self::TYPE_NOTIFICATION, false);

        return $this;
    }

    /**
     * Set HTTP code
     *
     * @param int $code
     * @return Mage_Api_Model_Server_MessageHandler
     */
    public function setHttpCode($code)
    {
        if ((int) $code > $this->_httpCode) {
            $this->_httpCode = (int) $code;
        }
        return $this;
    }

    /**
     * Get HTTP code
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->_httpCode;
    }

    /**
     * Get response messages
     *
     * @param string $type
     * @return array|SimpleXMLElement
     * @throws Exception
     */
    public function getResponseMessages($type = self::OUTPUT_ARRAY)
    {
        switch ($type) {
            case self::OUTPUT_ARRAY:
                $result = $this->_responseMessages;
                break;

            case self::OUTPUT_XML:
                $result = new SimpleXMLElement('<messages/>');
                ${self::TYPE_ERROR} = -1;
                ${self::TYPE_NOTIFICATION} = -1;
                foreach ($this->_responseMessages as $message) {
                    $i = ++${$message['type']};
                    $result->{$message['type']}[$i]->domain  = $message['domain'];
                    $result->{$message['type']}[$i]->code    = $message['code'];
                    $result->{$message['type']}[$i]->message = $message['message'];
                }
                break;

            default:
                throw new Exception('Invalid output type.');
                break;
        }
        return $result;
    }

    /**
     * Set flag of using text messages in the output
     *
     * @param boolean $flag
     */
    public function setUseTextMessages($flag)
    {
        $this->_useTextMessages = $flag;
    }

    /**
     * Get flag of using text messages in the output
     *
     * @return boolean
     */
    public function isUseTextMessages()
    {
        return $this->_useTextMessages;
    }

    /**
     * Get config preset messages list
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
