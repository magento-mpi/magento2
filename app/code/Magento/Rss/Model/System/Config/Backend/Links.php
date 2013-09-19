<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
namespace Magento\Rss\Model\System\Config\Backend;

class Links extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * @param \Magento\Core\Model\Cache\TypeListInterface $typeList
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Cache\TypeListInterface $typeList,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_typeList = $typeList;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Invalidate cache type, when value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_typeList->invalidate(\Magento\Core\Block\AbstractBlock::CACHE_GROUP);
        }
    }
}
