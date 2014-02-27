<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Model\System\Config\Backend;

/**
 * Cache cleaner backend model
 *
 */
class Links extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Invalidate cache type, when value was changed
     *
     * @return void
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_cacheTypeList->invalidate(\Magento\View\Element\AbstractBlock::CACHE_GROUP);
        }
    }
}
