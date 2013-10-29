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
 * Helper for working with server ip address
 */
class ServerAddress
{
    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    public function __construct(
        \Magento\App\RequestInterface $httpRequest
    ) {
        $this->_request = $httpRequest;
    }

    /**
     * Retrieve Server IP address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getServerAddress($ipToLong = false)
    {
        $address = $this->_request->getServer('SERVER_ADDR');
        if (!$address) {
            return false;
        }
        return $ipToLong ? ip2long($address) : $address;
    }
}
