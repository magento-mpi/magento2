<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event data helper
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
class Magento_CatalogEvent_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'catalog/magento_catalogevent/enabled';

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retreive event image url
     *
     * @param Magento_CatalogEvent_Model_Event
     * @return string|boolean
     */
    public function getEventImageUrl($event)
    {
        if ($event->getImage()) {
            return $event->getImageUrl();
        }

        return false;
    }

    /**
     * Retrieve configuration value for enabled of catalog event
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ENABLED);
    }
}
