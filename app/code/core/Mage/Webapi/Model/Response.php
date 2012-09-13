<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API Response model
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// TODO: Try to merge Webapi response with Mage_Core_Controller_Response_Http to have the single class
class Mage_Webapi_Model_Response extends Zend_Controller_Response_Http
{
    /**
     * Character set which must be used in response
     */
    const RESPONSE_CHARSET = 'utf-8';

    /**#@+
     * Default message types
     */
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR   = 'error';
    const MESSAGE_TYPE_WARNING = 'warning';
    /**#@- */

    /**
     * Messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Set header appropriate to specified MIME type
     *
     * @param string $mimeType MIME type
     * @return Mage_Webapi_Model_Response
     */
    public function setMimeType($mimeType)
    {
        return $this->setHeader('Content-Type', "{$mimeType}; charset=" . self::RESPONSE_CHARSET, true);
    }

    /**
     * Add message to response
     *
     * @param string $message
     * @param string $code
     * @param array $params
     * @param string $type
     * @return Mage_Webapi_Model_Response
     */
    public function addMessage($message, $code, $params = array(), $type = self::MESSAGE_TYPE_ERROR)
    {
        $params['message'] = $message;
        $params['code'] = $code;
        $this->_messages[$type][] = $params;
        return $this;
    }

    /**
     * Has messages
     *
     * @return bool
     */
    public function hasMessages()
    {
        return (bool)count($this->_messages) > 0;
    }

    /**
     * Return messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages
     *
     * @return Mage_Webapi_Model_Response
     */
    public function clearMessages()
    {
        $this->_messages = array();
        return $this;
    }
}
