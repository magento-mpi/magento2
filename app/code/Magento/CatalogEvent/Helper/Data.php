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
namespace Magento\CatalogEvent\Helper;

use Magento\App\Helper\AbstractHelper;
use Magento\App\Helper\Context;
use Magento\CatalogEvent\Model\Event;
use Magento\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'catalog/magento_catalogevent/enabled';

    /**
     * Core store config
     *
     * @var Config
     */
    protected $_storeConfig;

    /**
     * @param Context $context
     * @param Config $coreStoreConfig
     */
    public function __construct(
        Context $context,
        Config $coreStoreConfig
    ) {
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Retrieve event image url
     *
     * @param Event $event
     * @return string|false
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_ENABLED, \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE);
    }
}
