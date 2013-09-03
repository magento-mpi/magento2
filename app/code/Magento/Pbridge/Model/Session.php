<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Initialize Enterprise Pbridge session namespace
     *
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
