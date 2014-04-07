<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System config translate inline fields backend model
 */
namespace Magento\Backend\Model\Config\Backend;

class Translate extends \Magento\App\Config\Value
{
    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * Path to config node with list of caches
     *
     * @var string
     */
    const XML_PATH_INVALID_CACHES = 'dev/translate_inline/invalid_caches';

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * Constructor
     *
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_storeConfig = $coreStoreConfig;
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Set status 'invalidate' for blocks and other output caches
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $types = array_keys(
            $this->_storeConfig->getValue(
                self::XML_PATH_INVALID_CACHES,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        if ($this->isValueChanged()) {
            $this->_cacheTypeList->invalidate($types);
        }

        return $this;
    }
}
