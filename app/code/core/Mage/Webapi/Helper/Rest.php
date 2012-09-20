<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rest
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * REST API helper
 */
// TODO: Get rid of this class
class Mage_Webapi_Helper_Rest extends Mage_Core_Helper_Abstract
{
    /**#@+
     *  Default error messages
     */
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data invalid.'; //error while checking data inside method
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    /**#@-*/

    /**#@+
     *  Default success messages
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource updated successful.';
    /**#@-*/

    /**
     * Request interpret adapters
     */
    const XML_PATH_WEBAPI_REQUEST_INTERPRETERS = 'global/webapi/rest/request/interpreters';

    /**
     * Response render adapters
     */
    const XML_PATH_WEBAPI_RESPONSE_RENDERS = 'global/webapi/rest/response/renders';

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getRequestInterpreterAdapters()
    {
        return (array)Mage::app()->getConfig()->getNode(self::XML_PATH_WEBAPI_REQUEST_INTERPRETERS);
    }

    /**
     * Get interpreter type for Request body according to Content-type HTTP header
     *
     * @return array
     */
    public function getResponseRenderAdapters()
    {
        return (array)Mage::app()->getConfig()->getNode(self::XML_PATH_WEBAPI_RESPONSE_RENDERS);
    }

    /**
     * Throw exception to stop execution
     *
     * @param string $message
     * @param int $code
     * @throws Mage_Webapi_Exception|Exception
     */
    public function critical($message, $code = null)
    {
        if ($code === null) {
            $errors = $this->_getCriticalErrors();
            if (!isset($errors[$message])) {
                throw new Exception(
                    sprintf('Invalid error "%s" or error code missed.', $message),
                    Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR
                );
            }
            $code = $errors[$message];
        }
        throw new Mage_Webapi_Exception($message, $code);
    }

    /**
     * Retrieve array with critical errors mapped to HTTP codes
     *
     * @return array
     */
    protected function _getCriticalErrors()
    {
        return array(
            '' => Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST,
            self::RESOURCE_NOT_FOUND => Mage_Webapi_Controller_Front_Rest::HTTP_NOT_FOUND,
            self::RESOURCE_METHOD_NOT_ALLOWED => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_METHOD_NOT_IMPLEMENTED => Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_DATA_PRE_VALIDATION_ERROR => Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST,
            self::RESOURCE_INTERNAL_ERROR => Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR,
            self::RESOURCE_UNKNOWN_ERROR => Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST,
            self::RESOURCE_REQUEST_DATA_INVALID => Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST,
        );
    }
}
