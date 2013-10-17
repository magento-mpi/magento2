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
 *
 */
class ServerAddress
{
    /**
     * Request object
     *
     * @var \Magento\Core\Controller\Request\HttpProxy
     */
    protected $_request;

    public function __construct(
        \Magento\Core\Controller\Request\HttpProxy $httpRequest
    ) {
        $this->_request = $httpRequest;
    }

    public function getServerAddress()
    {

    }
}
