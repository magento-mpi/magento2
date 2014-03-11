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
 * Library for working with client ip address
 */
class RemoteAddress
{
    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * Remote address cache
     *
     * @var string
     */
    protected $remoteAddress;

    /**
     * @var array
     */
    protected $alternativeHeaders;

    /**
     * @param \Magento\App\RequestInterface $httpRequest
     * @param array $alternativeHeaders
     */
    public function __construct(
        \Magento\App\RequestInterface $httpRequest,
        array $alternativeHeaders = array()
    ) {
        $this->request = $httpRequest;
        $this->alternativeHeaders = $alternativeHeaders;
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddress($ipToLong = false)
    {
        if (is_null($this->remoteAddress)) {
            foreach ($this->alternativeHeaders as $var) {
                if ($this->request->getServer($var, false)) {
                    $this->remoteAddress = $this->request->getServer($var);
                    break;
                }
            }

            if (!$this->remoteAddress) {
                $this->remoteAddress = $this->request->getServer('REMOTE_ADDR');
            }
        }

        if (!$this->remoteAddress) {
            return false;
        }

        return $ipToLong ? ip2long($this->remoteAddress) : $this->remoteAddress;
    }
}
