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
    protected $_request;

    /**
     * Remote address cache
     *
     * @var string
     */
    protected $_remoteAddress;

    /**
     * @var array
     */
    protected $_alternativeHeaders;

    /**
     * @param \Magento\App\RequestInterface $httpRequest
     * @param array $alternativeHeaders
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\App\RequestInterface $httpRequest,
        $alternativeHeaders = array()
    ) {
        $this->_request = $httpRequest;

        if (!is_array($alternativeHeaders)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value of type "%s" given while array is expected as alternative headers',
                gettype($alternativeHeaders)
            ));
        }

        $this->_alternativeHeaders = $alternativeHeaders;
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
            foreach ($this->_alternativeHeaders as $var) {
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
}
