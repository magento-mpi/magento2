<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Session_Generic extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $sessionNamespace
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($eventManager, $coreHttp, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
