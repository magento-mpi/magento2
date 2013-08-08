<?php
/**
 * JSON interpreter of REST request content.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_Json implements
    Mage_Webapi_Controller_Request_Rest_InterpreterInterface
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Magento_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /** @var Magento_Core_Model_App */
    protected $_app;

    /**
     * @param Magento_Core_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_App $app
     */
    public function __construct(Magento_Core_Model_Factory_Helper $helperFactory, Magento_Core_Model_App $app)
    {
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_app = $app;
    }

    /**
     * Parse Request body into array of params.
     *
     * @param string $encodedBody Posted content from request.
     * @return array|null Return NULL if content is invalid.
     * @throws InvalidArgumentException
     * @throws Mage_Webapi_Exception If decoding error was encountered.
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
            /** @var Magento_Core_Helper_Data $jsonHelper */
            $jsonHelper = $this->_helperFactory->get('Magento_Core_Helper_Data');
            $decodedBody = $jsonHelper->jsonDecode($encodedBody);
        } catch (Zend_Json_Exception $e) {
            if (!$this->_app->isDeveloperMode()) {
                throw new Mage_Webapi_Exception($this->_helper->__('Decoding error.'),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST);
            } else {
                throw new Mage_Webapi_Exception(
                    'Decoding error: ' . PHP_EOL . $e->getMessage() . PHP_EOL . $e->getTraceAsString(),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }

        }
        return $decodedBody;
    }
}
