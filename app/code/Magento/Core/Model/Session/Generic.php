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
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $sessionNamespace
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($eventManager, $coreHttp, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
