<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cleanup blocks HTML cache
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Backend;

class Active extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_app = $app;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_websiterestriction_config_active';

    /**
     * Cleanup blocks HTML cache if value has been changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_app->cleanCache(array(\Magento\Core\Model\Store::CACHE_TAG, \Magento\Cms\Model\Block::CACHE_TAG));
        }
        return parent::_afterSave();
    }
}
