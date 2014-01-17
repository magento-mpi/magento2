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
 * Library for working with server ip address
 */
class ServerAddress
{
    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\App\RequestInterface $httpRequest
     */
    public function __construct(
        \Magento\App\RequestInterface $httpRequest
    ) {
        $this->request = $httpRequest;
    }

    /**
     * Retrieve Server IP address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getServerAddress($ipToLong = false)
    {
        $address = $this->request->getServer('SERVER_ADDR');
        if (!$address) {
            return false;
        }
        return $ipToLong ? ip2long($address) : $address;
    }
}
