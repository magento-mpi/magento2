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
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $sessionNamespace
     * @param null|string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig,
        $sessionNamespace,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $coreConfig, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
