<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog session model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $data
     * @param string $sessionName
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($logger, $eventManager, $coreHttp, $data);
        $this->init('catalog', $sessionName);
    }

    public function getDisplayMode()
    {
        return $this->_getData('display_mode');
    }

}
