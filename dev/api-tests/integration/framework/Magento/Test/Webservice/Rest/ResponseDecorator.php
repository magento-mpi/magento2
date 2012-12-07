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

/**
 * REST Response class
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 * @method boolean isError() isError()
 * @method int getStatus() getStatus()
 * @method boolean isSuccessful() isSuccessful()
 * @method boolean isRedirect() isRedirect()
 * @method string getRawBody() getRawBody()
 * @method string getVersion() getVersion()
 * @method int getStatus() getStatus()
 * @method string getMessage() getMessage()
 * @method array getHeaders() getHeaders()
 * @method string|array|null getHeader($header) getHeader($header)
 * @method string getHeadersAsString($status_line, $br) getHeadersAsString($status_line, $br)
 * @method string asString($br)
 */
class Magento_Test_Webservice_Rest_ResponseDecorator
{
    protected $_zendHttpResponse = null;

    public function __construct(Zend_Http_Response $zendHttpResponse)
    {
        $this->_zendHttpResponse = $zendHttpResponse;
    }

    /**
     * Get the response body as array
     *
     * @return array
     */
    public function getBody()
    {
        list($contentType) = explode(';', $this->_zendHttpResponse->getHeader('Content-Type'));
        $interpreter = Magento_Test_Webservice_Rest_Interpreter_Factory::getInterpreter($contentType);

        return $interpreter->decode($this->_zendHttpResponse->getBody());
    }

    /**
     * Proxy method calls to decorated object
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
       return call_user_func_array(array($this->_zendHttpResponse, $method), $arguments);
    }

    /**
     * Proxy __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_zendHttpResponse->__toString();
    }
}
