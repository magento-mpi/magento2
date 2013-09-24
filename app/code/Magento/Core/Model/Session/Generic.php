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
     * @param Magento_Core_Model_Session_Context $context
     * @param array $sessionNamespace
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        Magento_Core_Model_Session_Context $context,
        $sessionNamespace,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($context, $data);
        $this->init($sessionNamespace, $sessionName);
    }
}
