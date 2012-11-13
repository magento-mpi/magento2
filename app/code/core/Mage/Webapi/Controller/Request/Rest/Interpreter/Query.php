<?php
/**
 * Interpreter of REST request content encoded in query string.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_Query implements
    Mage_Webapi_Controller_Request_Rest_InterpreterInterface
{
    const URI_VALIDATION_PATTERN = "/^(?:%[[:xdigit:]]{2}|[A-Za-z0-9-_.!~*'()\[\];\/?:@&=+$,])*$/";

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
     * Parse request body into array of params.
     *
     * @param string $body Posted content from request
     * @return array
     * @throws InvalidArgumentException
     * @throws Mage_Webapi_Exception
     */
    public function interpret($body)
    {
        if (!is_string($body)) {
            throw new InvalidArgumentException(sprintf('Invalid data type "%s". String expected.', gettype($body)));
        }
        if (!$this->_validateQuery($body)) {
            throw new Mage_Webapi_Exception($this->_helper->__('Invalid data type. Check Content-Type.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        $data = array();
        parse_str($body, $data);
        return $data;
    }

    /**
     * Returns true if and only if the query string passes validation.
     *
     * @param  string $query The query to validate
     * @return boolean
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    protected function _validateQuery($query)
    {
        return preg_match(self::URI_VALIDATION_PATTERN, $query);
    }
}
