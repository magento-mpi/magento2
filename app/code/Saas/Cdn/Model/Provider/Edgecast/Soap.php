<?php
/**
 * Content delivery network
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Cdn_Model_Provider_Edgecast_Soap extends SoapClient
{
    /**
     * Path to cdn provider connection config
     */
    const XML_CDN_CONNECTION_NODE = 'global/cdn_provider/connection';

    /**
     * Soap connection settings
     *
     * @var array
     */
    protected $_connectionSettings = array(
        'trace'        => 1,
        'exceptions'   => 1,
        'soap_version' => SOAP_1_1,
        'encoding'     => 'ISO-8859-1',
    );

    /**
     * Initialize Edgecast CDN soap default state
     *
     * @param Mage_Core_Model_Config $config
     * @throws Saas_Cdn_Exception
     */
    public function __construct(Mage_Core_Model_Config $config)
    {
        $connectionNode = $config->getNode(self::XML_CDN_CONNECTION_NODE);
        if (!$connectionNode) {
            throw new Saas_Cdn_Exception('Unable to get CDN connection node');
        }
        $nodeConfig = $connectionNode->asArray();

        //workaround for SoapClient bugs
        //https://bugs.php.net/search.php?cmd=display&search_for=SOAP-ERROR%3A+Parsing+WSDL%3A+Couldn't+load+from
        try {
            set_error_handler(array($this, 'convertWsdlErrorIntoException'));
            @ parent::__construct($nodeConfig['wsdl_url'], $this->_connectionSettings);
            restore_error_handler();
        } catch (Exception $e) {
            throw new Saas_Cdn_Exception($e->getMessage());
        }
    }

    /**
     * Try to convert SoapClient error into exception
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @throws Saas_Cdn_Exception
     */
    public function convertWsdlErrorIntoException($errno, $errstr, $errfile = NULL, $errline = NULL, $errcontext = NULL)
    {
        $wsdlUrl = isset($errcontext['wsdl']) ? $errcontext['wsdl'] : '';
        throw new Saas_Cdn_Exception('SOAP-ERROR: Parsing WSDL: Couldn\'t load from' . $wsdlUrl);

    }

    /**
     * SoapClient::__call() is depricated, so this method redirects everyting to __soapCall().
     *
     * @param string $function_name
     * @param array $arguments
     * @return mixed
     */
    public function __call($function_name, $arguments)
    {
        return $this->__soapCall($function_name, $arguments);
    }
}
