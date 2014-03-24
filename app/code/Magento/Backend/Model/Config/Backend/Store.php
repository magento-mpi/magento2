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
 * Backend add store code to url backend
 */
namespace Magento\Backend\Model\Config\Backend;

class Store extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\App\Config\MutableScopeConfigInterface
     */
    protected $_mutableConfig;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\App\Config\MutableScopeConfigInterface $mutableConfig
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\App\Config\MutableScopeConfigInterface $mutableConfig,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
        $this->_mutableConfig = $mutableConfig;
    }

    /**
     * @return void
     */
    protected function _afterSave()
    {
        $this->_mutableConfig->setValue(
            \Magento\Store\Model\Store::XML_PATH_STORE_IN_URL,
            $this->getValue(),
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_cacheManager->clean();
    }
}
