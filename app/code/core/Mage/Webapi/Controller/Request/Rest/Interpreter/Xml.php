<?php
/**
 * XML interpreter of REST request content.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_Xml implements
    Mage_Webapi_Controller_Request_Rest_InterpreterInterface
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /** @var Mage_Xml_Parser */
    protected $_xmlParser;

    /** @var Mage_Core_Model_App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Xml_Parser $xmlParser
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Xml_Parser $xmlParser,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_App $app
    ) {
        $this->_xmlParser = $xmlParser;
        $this->_helperFactory = $helperFactory;
        $this->_helper = $this->_helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_app = $app;
    }

    /**
     * Load error string.
     *
     * Is null if there was no error while loading
     *
     * @var string
     */
    protected $_errorMessage = null;

    /**
     * Convert XML document into array.
     *
     * @param string $xmlRequestBody XML document
     * @return array Data converted from XML document to array. Root node is excluded from response.
     * @throws InvalidArgumentException In case of invalid argument type.
     * @throws Mage_Webapi_Exception If decoding error occurs.
     */
    public function interpret($xmlRequestBody)
    {
        if (!is_string($xmlRequestBody)) {
            throw new InvalidArgumentException(
                sprintf('Invalid data type "%s". String is expected.', gettype($xmlRequestBody))
            );
        }
        /** Disable external entity loading to prevent possible vulnerability */
        $previousLoaderState = libxml_disable_entity_loader(true);
        set_error_handler(array($this, 'handleErrors'));

        $this->_xmlParser->loadXML($xmlRequestBody);

        restore_error_handler();
        libxml_disable_entity_loader($previousLoaderState);

        /** Process errors during XML parsing. */
        if ($this->_errorMessage !== null) {
            if (!$this->_app->isDeveloperMode()) {
                $exceptionMessage = $this->_helper->__('Decoding error.');
            } else {
                $exceptionMessage = 'Decoding Error: ' . $this->_errorMessage;
            }
            throw new Mage_Webapi_Exception($exceptionMessage, Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        $data = $this->_xmlParser->xmlToArray();
        /** Data will always have exactly one element so it is safe to call reset here. */
        return reset($data);
    }

    /**
     * Handle any errors during XML loading.
     *
     * @param integer $errorNumber
     * @param string $errorMessage
     * @param string $errorFile
     * @param integer $errorLine
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleErrors($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
        if (is_null($this->_errorMessage)) {
            $this->_errorMessage = $errorMessage;
        } else {
            $this->_errorMessage .= $errorMessage;
        }
    }
}