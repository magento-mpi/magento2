<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP\PhpEnvironment;

/**
 * Helper for working with client ip address
 *
 */
class RemoteAddress
{
    const XML_NODE_REMOTE_ADDRESS_HEADERS  = 'global/remote_addr_headers';

    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_config;

    /**
     * Remote address cache
     *
     * @var string
     */
    protected $_remoteAddress;

    public function __construct(
        \Magento\App\RequestInterface $httpRequest,
        \Magento\Core\Model\ConfigInterface $config
    ) {
        $this->_request = $httpRequest;
        $this->_config = $config;
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddress($ipToLong = false)
    {
        if (is_null($this->_remoteAddress)) {
            $headers = $this->getRemoteAddressHeaders();
            foreach ($headers as $var) {
                if ($this->_request->getServer($var, false)) {
                    $this->_remoteAddress = $_SERVER[$var];
                    break;
                }
            }

            if (!$this->_remoteAddress) {
                $this->_remoteAddress = $this->_request->getServer('REMOTE_ADDR');
            }
        }

        if (!$this->_remoteAddress) {
            return false;
        }

        return $ipToLong ? ip2long($this->_remoteAddress) : $this->_remoteAddress;
    }

    /**
     * Retrieve Remote Addresses Additional check Headers
     *
     * @return array
     */
    public function getRemoteAddressHeaders()
    {
        $headers = array();
        $element = $this->_config->getNode(self::XML_NODE_REMOTE_ADDRESS_HEADERS);
        if ($element instanceof \Magento\Core\Model\Config\Element) {
            foreach ($element->children() as $node) {
                $headers[] = (string)$node;
            }
        }

        return $headers;
    }
}
