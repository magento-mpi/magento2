<?php
/**
 * Magento-specific SOAP fault.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap;

class Fault extends \RuntimeException
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
    const NODE_DETAIL_CODE = 'Code';
    const NODE_DETAIL_PARAMETERS = 'Parameters';
    /** Note that parameter node must be unique in scope of all complex types declared in WSDL */
    const NODE_DETAIL_PARAMETER = 'GenericFaultParameter';
    const NODE_DETAIL_PARAMETER_KEY = 'key';
    const NODE_DETAIL_PARAMETER_VALUE = 'value';
    const NODE_DETAIL_TRACE = 'Trace';
    const NODE_DETAIL_WRAPPER = 'GenericFault';
    /**#@-*/

    /** @var string */
    protected $_soapFaultCode;

    /** @var string */
    protected $_errorCode;

    /**
     * Parameters are extracted from exception and can be inserted into 'Detail' node as 'Parameters'.
     *
     * @var array
     */
    protected $_parameters = array();

    /**
     * Fault name is used for details wrapper node name generation.
     *
     * @var string
     */
    protected $_faultName = '';

    /**
     * Details that are used to generate 'Detail' node of SoapFault.
     *
     * @var array
     */
    protected $_details = array();

    /** @var \Magento\Core\Model\App */
    protected $_application;

    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServer;

    /**
     * Construct exception.
     *
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Webapi\Exception $previousException
     * @param \Magento\Webapi\Model\Soap\Server $soapServer
     */
    public function __construct(
        \Magento\Core\Model\App $application,
        \Magento\Webapi\Model\Soap\Server $soapServer,
        \Magento\Webapi\Exception $previousException
    ) {
        parent::__construct($previousException->getMessage(), $previousException->getCode(), $previousException);
        $this->_soapCode = $previousException->getOriginator();
        $this->_parameters = $previousException->getDetails();
        $this->_errorCode = $previousException->getCode();
        $this->_application = $application;
        $this->_soapServer = $soapServer;
        $this->_setFaultName($previousException->getName());
    }

    /**
     * Render exception as XML.
     *
     * @return string
     */
    public function toXml()
    {
        if ($this->_application->isDeveloperMode()) {
            $this->addDetails(array(self::NODE_DETAIL_TRACE => "<![CDATA[{$this->getTraceAsString()}]]>"));
        }
        if ($this->getParameters()) {
            $this->addDetails(array(self::NODE_DETAIL_PARAMETERS => $this->getParameters()));
        }
        if ($this->getErrorCode()) {
            $this->addDetails(array(self::NODE_DETAIL_CODE => $this->getErrorCode()));
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
     * Receive SOAP fault name.
     *
     * @return string
     */
    public function getFaultName()
    {
        return $this->_faultName;
    }

    /**
     * Define current SOAP fault name. It is used as a name of the wrapper node for SOAP fault details.
     *
     * @param $exceptionName
     */
    protected function _setFaultName($exceptionName)
    {
        if ($exceptionName) {
            $contentType = $this->_application->getRequest()->getHeader('Content-Type');
            /** SOAP action is specified in content type header if content type is application/soap+xml */
            if (preg_match('|application/soap\+xml.+action="(.+)".*|', $contentType, $matches)) {
                $soapAction = $matches[1];
                $this->_faultName = ucfirst($soapAction) . ucfirst($exceptionName) . 'Fault';
            }
        }
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
     * @return \Magento\Webapi\Model\Soap\Fault
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
    public function getSoapFaultMessage($reason, $code, $details = null)
    {
        $detailXml = $this->_generateDetailXml($details);
        $language = $this->getLanguage();
        $detailsNamespace = !empty($detailXml)
            ? 'xmlns:m="' . urlencode($this->_soapServer->generateUri(true)) . '"'
            : '';
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
         $detailXml
      </env:Fault>
   </env:Body>
</env:Envelope>
FAULT_MESSAGE;
        return $message;
    }

    /**
     * Generate 'Detail' node content.
     *
     * In case when fault name is undefined, no 'Detail' node is generated.
     *
     * @param array $details
     * @return string
     */
    protected function _generateDetailXml($details)
    {
        $detailsXml = '';
        if (is_array($details) && !empty($details)) {
            $detailsXml = $this->_convertDetailsToXml($details);
            if ($detailsXml) {
                $errorDetailsNode = $this->getFaultName() ? $this->getFaultName() :self::NODE_DETAIL_WRAPPER;
                $detailsXml = "<env:Detail><m:{$errorDetailsNode}>"
                    . $detailsXml . "</m:{$errorDetailsNode}></env:Detail>";
            } else {
                $detailsXml = '';
            }
        }
        return $detailsXml;
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
            switch ($detailNode) {
                case self::NODE_DETAIL_CODE:
                    // break is intentionally omitted
                case self::NODE_DETAIL_TRACE:
                    if (is_string($detailValue) || is_numeric($detailValue)) {
                        $detailsXml .= "<m:$detailNode>" . htmlspecialchars($detailValue) . "</m:$detailNode>";
                    }
                    break;
                case self::NODE_DETAIL_PARAMETERS:
                    $detailsXml .= $this->_getParametersXml($detailValue, $detailNode, $detailsXml);
                    break;
            }
        }
        return $detailsXml;
    }

    /**
     * Generate XML for parameters.
     *
     * @param array $parameters
     * @return string
     */
    protected function _getParametersXml($parameters)
    {
        $result = '';
        if (is_array($parameters)) {
            $paramsXml = '';
            foreach ($parameters as $parameterName => $parameterValue) {
                if (is_string($parameterName) && (is_string($parameterValue) || is_numeric($parameterValue))) {
                    $keyNode = self::NODE_DETAIL_PARAMETER_KEY;
                    $valueNode = self::NODE_DETAIL_PARAMETER_VALUE;
                    $parameterNode = self::NODE_DETAIL_PARAMETER;
                    $paramsXml .= "<m:$parameterNode><m:$keyNode>$parameterName</m:$keyNode><m:$valueNode>"
                        . htmlspecialchars($parameterValue) . "</m:$valueNode></m:$parameterNode>";
                }
            }
            if (!empty($paramsXml)) {
                $parametersNode = self::NODE_DETAIL_PARAMETERS;
                $result = "<m:$parametersNode>" . $paramsXml . "</m:$parametersNode>";
            }
        }
        return $result;
    }
}
