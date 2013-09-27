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
     * @param array $sessionNamespace
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
