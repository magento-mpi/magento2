<?php
/**
 * Web API response.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response extends Zend_Controller_Response_Http
{
    /**
     * Character set which must be used in response.
     */
    const RESPONSE_CHARSET = 'utf-8';

    /**#@+
     * Default message types.
     */
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_WARNING = 'warning';
    /**#@- */

    /**
     * Messages.
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Set header appropriate to specified MIME type.
     *
     * @param string $mimeType MIME type
     * @return Magento_Webapi_Controller_Response
     */
    public function setMimeType($mimeType)
    {
        return $this->setHeader('Content-Type', "{$mimeType}; charset=" . self::RESPONSE_CHARSET, true);
    }

    /**
     * Add message to response.
     *
     * @param string $message
     * @param string $code
     * @param array $params
     * @param string $type
     * @return Magento_Webapi_Controller_Response
     */
    public function addMessage($message, $code, $params = array(), $type = self::MESSAGE_TYPE_ERROR)
    {
        $params['message'] = $message;
        $params['code'] = $code;
        $this->_messages[$type][] = $params;
        return $this;
    }

    /**
     * Has messages.
     *
     * @return bool
     */
    public function hasMessages()
    {
        return (bool)count($this->_messages) > 0;
    }

    /**
     * Return messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages.
     *
     * @return Magento_Webapi_Controller_Response
     */
    public function clearMessages()
    {
        $this->_messages = array();
        return $this;
    }
}
