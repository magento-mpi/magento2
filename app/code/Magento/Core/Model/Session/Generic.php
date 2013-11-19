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
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Zend\Session\Config\ConfigInterface $sessionConfig
     * @param string $sessionNamespace
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Context $context,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Zend\Session\Config\ConfigInterface $sessionConfig,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $sidResolver, $sessionConfig, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
