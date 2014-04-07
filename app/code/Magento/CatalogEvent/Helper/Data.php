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
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
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
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
