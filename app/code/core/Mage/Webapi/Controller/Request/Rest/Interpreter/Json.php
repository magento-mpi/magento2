<?php
/**
 * JSON interpreter of REST request content.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_Json implements
    Mage_Webapi_Controller_Request_Rest_InterpreterInterface
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Mage_Webapi_Helper_Data');
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
                'Invalid data type "%s". String expected.',
                gettype($encodedBody)
            ));
        }
        try {
            /** @var Mage_Core_Helper_Data $jsonHelper */
            $jsonHelper = $this->_helperFactory->get('Mage_Core_Helper_Data');
            $decodedBody = $jsonHelper->jsonDecode($encodedBody);
        } catch (Zend_Json_Exception $e) {
            throw new Mage_Webapi_Exception($this->_helper->__('Decoding error.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $decodedBody;
    }
}
