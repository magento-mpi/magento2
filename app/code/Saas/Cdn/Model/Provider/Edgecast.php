<?php
/**
 * Content delivery network
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Cdn_Model_Provider_Edgecast implements Saas_Cdn_Model_CdnInterface
{
    /**
     * Connection options
     */
    const XML_WSDL_URL = 'cdn_provider/connection/wsdl_url';

    /**
     * Path to media dir into XML config
     */
    const XML_MEDIA = 'global/cdn_provider/secure/media';


    /**
     * Path to static dir into XML config
     */
    const XML_STATIC = 'global/cdn_provider/secure/static';

    /**
     * May be used to delete all nodes in some directory
     */
    const ALL_NODES = '*';

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @var array
     */
    protected $_queryTemplate = array();

    /**
     * @var Saas_Cdn_Model_Provider_Edgecast_SoapProxy
     */
    protected $_client;

    /**
     * Store list of directories that should be
     *
     * @var array
     */
    protected $_dirsMapping = array();

    /**
     * Initialize Edgecast CDN provider data
     *
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Saas_Cdn_Model_Provider_Edgecast_Soap $soapClientProxy
     *
     * @throws Saas_Cdn_Exception
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Saas_Cdn_Model_Provider_Edgecast_Soap $soapClientProxy
    )
    {
        $this->_log = $logger;
        $this->_client = $soapClientProxy;

        $this->_dirsMapping = array(
            $dirs->getDir(Mage_Core_Model_Dir::MEDIA)       => (string)$config->getNode(self::XML_MEDIA),
            $dirs->getDir(Mage_Core_Model_Dir::STATIC_VIEW) => (string)$config->getNode(self::XML_STATIC),
        );

        $connectionNode = $config->getNode(Saas_Cdn_Model_Provider_Edgecast_Soap::XML_CDN_CONNECTION_NODE);
        if (!$connectionNode) {
            throw new Saas_Cdn_Exception('Unable to get CDN connection node');
        }

        $this->_config = $connectionNode->asArray();
        $this->_queryTemplate = array(
            'strCredential' => "c:bas:{$this->_config['email']}:{$this->_config['password']}",
            'strCustomerId' => $this->_config['customer_id'],
            'intMediaType'  => 8,
            'strPath'       => '',
        );
    }

    /**
     * Delete file from CDN
     *
     * @param string $path
     * @return boolean
     * @throws Saas_Cdn_Exception
     */
    public function deleteFile($path)
    {
        $path = $this->_getCdnPath($path);
        $query = array_merge($this->_queryTemplate, array('strPath' => $path));
        try {
            $result = $this->_client->PurgeFileFromEdge($query);
            if (!property_exists($result, 'PurgeFileFromEdgeResult')) {
                throw new Saas_Cdn_Exception($this->_getErrorResponse());
            } else if ($result->PurgeFileFromEdgeResult !== 0) {
                throw new Saas_Cdn_Exception('Unable to delete image from CDN');
            }
        } catch (Exception $e) {
            $this->_log->logException($e);
        }

        return true;
    }

    /**
     * Delete files and directories from CDN recursively
     *
     * @param string $path
     * @return bool
     */
    public function deleteRecursively($path)
    {
        $path = rtrim ($path, '/') . '/' . self::ALL_NODES;
        return $this->deleteFile($path);
    }

    /**
     * @param string $path
     * @return mixed
     * @throws Saas_Cdn_Exception
     */
    protected function _getCdnPath($path)
    {
        $replaced = false;
        foreach ($this->_dirsMapping as $dir => $url) {
            if (0 === strpos($path, $dir)) {
                $path = str_replace($dir, $url, $path);
                $replaced = true;
                break;
            }
        }
        if (!$replaced) {
            throw new Saas_Cdn_Exception('No mapping for supplied path.');
        }
        return $path;
    }

    /**
     * Retrieve last cdn error message
     *
     * @return string
     */
    protected function _getErrorResponse()
    {
        $xmlString = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $this->_client->__getLastResponse());
        $xml = simplexml_load_string($xmlString);
        return (string)$xml->soapBody->soapFault->faultstring;
    }

}
