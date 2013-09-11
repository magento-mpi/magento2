<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Session;

class Generic extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param string $sessionNamespace
     * @param string $sessionName
     */
    public function __construct($sessionNamespace, $sessionName = null)
    {
        $this->init($sessionNamespace, $sessionName);
    }
}
