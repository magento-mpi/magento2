<?php
/**
 * Magento-specific SOAP fault.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_Fault extends RuntimeException
{
    const FAULT_REASON_INTERNAL = 'Internal Error.';

    /**#@+
     * Fault codes that are used in SOAP faults.
     */
    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    /**#@+
     * Nodes that can appear in Detail node of SOAP fault.
     */
    const NODE_ERROR_DETAIL_CODE = 'Code';
    const NODE_ERROR_DETAIL_PARAMETERS = 'Parameters';
    const NODE_ERROR_DETAIL_TRACE = 'Trace';
    const NODE_ERROR_DETAILS = 'ErrorDetails';
    /**#@-*/

    /** @var string */
    protected $_soapFaultCode;

    /** @var string */
    protected $_errorCode;

    /** @var array */
    protected $_parameters;

    /**
     * Details that are used to generate 'Detail' node of SoapFault.
     *
     * @var array
     */
    protected $_details = array();

    /** @var Magento_Core_Model_App */
    protected $_application;

    /**
     * Construct exception.
     *
     * @param Magento_Core_Model_App $application
     * @param Magento_Webapi_Exception $previousException
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Webapi_Exception $previousException
    ) {
        parent::__construct($previousException->getMessage(), $previousException->getCode(), $previousException);
        $this->_soapCode = $previousException->getOriginator();
        $this->_parameters = $previousException->getDetails();
        $this->_errorCode = $previousException->getCode();
        $this->_application = $application;
    }

    /**
     * Render exception as XML.
     *
     * @return string
     */
    public function toXml()
    {
        if ($this->_application->isDeveloperMode()) {
            $this->addDetails(array(self::NODE_ERROR_DETAIL_TRACE => "<![CDATA[{$this->getTraceAsString()}]]>"));
        }
        if ($this->getParameters()) {
            $this->addDetails(array(self::NODE_ERROR_DETAIL_PARAMETERS => $this->getParameters()));
        }
        if ($this->getErrorCode()) {
            $this->addDetails(array(self::NODE_ERROR_DETAIL_CODE => $this->getErrorCode()));
        }

        return $this->getSoapFaultMessage($this->getMessage(), $this->getSoapCode(), $this->getDetails());
    }

    /**
     * Retrieve additional details about current fault.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Retrieve error code.
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }

    /**
     * Add details about current fault.
     *
     * @param array $details Associative array containing details about current fault
     * @return Magento_Webapi_Model_Soap_Fault
     */
    public function addDetails($details)
    {
        $this->_details = array_merge($this->_details, $details);
        return $this;
    }

    /**
     * Retrieve additional details about current fault.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->_details;
    }

    /**
     * Retrieve SOAP fault code.
     *
     * @return string
     */
    public function getSoapCode()
    {
        return $this->_soapCode;
    }

    /**
     * Retrieve SOAP fault language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_application->getLocale()->getLocale()->getLanguage();
    }

    /**
     * Generate SOAP fault message in XML format.
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param array|null $details Detailed reason message(s)
     * @return string
     */
    public function getSoapFaultMessage($reason, $code, $details)
    {
        if (is_array($details) && !empty($details)) {
            $detailsXml = $this->_convertDetailsToXml($details);
            $errorDetailsNode = self::NODE_ERROR_DETAILS;
            $detailsXml = $detailsXml
                ? "<env:Detail><m:{$errorDetailsNode}>" . $detailsXml . "</m:{$errorDetailsNode}></env:Detail>"
                : '';
        } else {
            $detailsXml = '';
        }
        $language = $this->getLanguage();
        $detailsNamespace = !empty($detailsXml) ? 'xmlns:m="http://magento.com"': '';
        $reason = htmlentities($reason);
        $message = <<<FAULT_MESSAGE
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" $detailsNamespace>
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>env:$code</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text xml:lang="$language">$reason</env:Text>
         </env:Reason>
         $detailsXml
      </env:Fault>
   </env:Body>
</env:Envelope>
FAULT_MESSAGE;
        return $message;
    }

    /**
     * Recursively convert details array into XML structure.
     *
     * @param array $details
     * @return string
     */
    protected function _convertDetailsToXml($details)
    {
        $detailsXml = '';
        foreach ($details as $detailNode => $detailValue) {
            $detailNode = htmlspecialchars($detailNode);
            if (is_numeric($detailNode)) {
                continue;
            }
            if (is_string($detailValue) || is_numeric($detailValue)) {
                $detailsXml .= "<m:$detailNode>" . htmlspecialchars($detailValue) . "</m:$detailNode>";
            } elseif (is_array($detailValue)) {
                $detailsXml .= "<m:$detailNode>" . $this->_convertDetailsToXml($detailValue) . "</m:$detailNode>";
            }
        }
        return $detailsXml;
    }
}
