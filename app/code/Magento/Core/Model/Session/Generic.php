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
     * @param \Magento\Core\Model\Session\Context $context
     * @param string $sessionNamespace
     * @param mixed $sessionName
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        $sessionNamespace,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
