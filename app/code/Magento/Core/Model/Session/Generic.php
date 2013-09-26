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
     * @param \Magento\Core\Model\Session\Validator $validator
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     * @param array $sessionNamespace
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        \Magento\Core\Model\Session\Validator $validator,
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($validator, $logger, $eventManager, $coreHttp, $coreStoreConfig, $coreConfig, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
