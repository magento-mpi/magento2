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
     * @param Magento_Core_Model_Session_Validator $validator
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $sessionNamespace
     * @param string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        $sessionNamespace,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($validator, $eventManager, $coreHttp, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
