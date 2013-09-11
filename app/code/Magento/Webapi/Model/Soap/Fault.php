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

    const FAULT_CODE_SENDER = 'Sender';
    const FAULT_CODE_RECEIVER = 'Receiver';

    /** @var string */
    protected $_soapCode;

    /**
     * Construct exception.
     *
     * @param string $reason
     * @param string $code
     * @param \Exception $previous
     */
    public function __construct(
        $reason = self::FAULT_REASON_INTERNAL,
        $code = self::FAULT_CODE_RECEIVER,
        \Exception $previous = null
    ) {
        parent::__construct($reason, 0, $previous);
        $this->_soapCode = $code;
    }

    /**
     * Render exception as XML.
     *
     * @param $isDeveloperMode
     * @return string
     */
    public function toXml($isDeveloperMode)
    {
        $details = null;
        if ($isDeveloperMode) {
            $details = array(
                'ExceptionTrace' => "<![CDATA[{$this->getTraceAsString()}]]>"
            );
        }

        // TODO: Implement Current language definition
        $language = 'en';
        return $this->getSoapFaultMessage($this->getMessage(), $this->getSoapCode(), $language, $details);
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
     * Generate SOAP fault message in XML format.
     *
     * @param string $reason Human-readable explanation of the fault
     * @param string $code SOAP fault code
     * @param string $language Reason message language
     * @param string|array|null $details Detailed reason message(s)
     * @return string
     */
    public function getSoapFaultMessage($reason, $code, $language, $details)
    {
        if (is_string($details)) {
            $detailsXml = "<env:Detail>" . htmlspecialchars($details) . "</env:Detail>";
        } elseif (is_array($details)) {
            $detailsXml = "<env:Detail>" . $this->_convertDetailsToXml($details) . "</env:Detail>";
        } else {
            $detailsXml = '';
        }
        $reason = htmlentities($reason);
        $message = <<<FAULT_MESSAGE
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
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
            if (is_string($detailValue)) {
                $detailsXml .= "<$detailNode>" . htmlspecialchars($detailValue) . "</$detailNode>";
            } elseif (is_array($detailValue)) {
                $detailsXml .= "<$detailNode>" . $this->_convertDetailsToXml($detailValue) . "</$detailNode>";
            }
        }
        return $detailsXml;
    }
}
