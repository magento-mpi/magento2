<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tax\Model\Config;

/**
 * Tax Config Notification
 *
 * @category   Mage
 * @package    Mage_Tax
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Notification extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Core\Model\Resource\Config
     */
    protected $_resourceConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param \Magento\Core\Model\Resource\Config $resourceConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        \Magento\Core\Model\Resource\Config $resourceConfig,
        array $data = array()
    ) {
        $this->_resourceConfig = $resourceConfig;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Prepare and store cron settings after save
     *
     * @return \Magento\Tax\Model\Config\Notification
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_resetNotificationFlag(\Magento\Tax\Model\Config::XML_PATH_TAX_NOTIFICATION_IGNORE_DISCOUNT);
            $this->_resetNotificationFlag(\Magento\Tax\Model\Config::XML_PATH_TAX_NOTIFICATION_IGNORE_PRICE_DISPLAY);
        }
        return parent::_afterSave($this);
    }

    /**
     * Reset flag for showing tax notifications
     *
     * @param string $path
     * @return \Magento\Tax\Model\Config\Notification
     */
    protected function _resetNotificationFlag($path)
    {
        $this->_resourceConfig->saveConfig($path, 0, \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT, 0);
        return $this;
    }
}