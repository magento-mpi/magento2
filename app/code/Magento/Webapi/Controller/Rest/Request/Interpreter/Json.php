<?php
/**
 * JSON interpreter of REST request content.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Request_Interpreter_Json implements
    Magento_Webapi_Controller_Rest_Request_InterpreterInterface
{
    /** @var Magento_Core_Helper_Data */
    protected $_helper;

    /** @var Magento_Core_Model_App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Helper_Data $helper
     * @param Magento_Core_Model_App $app
     */
    public function __construct(Magento_Core_Helper_Data $helper, Magento_Core_Model_App $app)
    {
        $this->_helper = $helper;
        $this->_app = $app;
    }

    /**
     * Parse Request body into array of params.
     *
     * @param string $encodedBody Posted content from request.
     * @return array|null Return NULL if content is invalid.
     * @throws InvalidArgumentException
     * @throws Magento_Webapi_Exception If decoding error was encountered.
     */
    public function interpret($encodedBody)
    {
        if (!is_string($encodedBody)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" data type is invalid. String is expected.',
                gettype($encodedBody)
            ));
        }
        try {
            $decodedBody = $this->_helper->jsonDecode($encodedBody);
        } catch (Zend_Json_Exception $e) {
            if (!$this->_app->isDeveloperMode()) {
                throw new Magento_Webapi_Exception(__('Decoding error.'),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST);
            } else {
                throw new Magento_Webapi_Exception(
                    __('Decoding error: %1%2%3%4', PHP_EOL, $e->getMessage(), PHP_EOL, $e->getTraceAsString()),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }
        }
        return $decodedBody;
    }
}
