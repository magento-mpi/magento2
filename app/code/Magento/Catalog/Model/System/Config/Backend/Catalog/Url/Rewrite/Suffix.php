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
 * Url rewrite suffix backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite;

class Suffix extends \Magento\Core\Model\Config\Value
{
    /**
     * Core url rewrite
     *
     * @var \Magento\Core\Helper\Url\Rewrite
     */
    protected $_coreUrlRewrite = null;

    /**
     * @param Magento_Core_Helper_Url_Rewrite $coreUrlRewrite
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Url\Rewrite $coreUrlRewrite,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreUrlRewrite = $coreUrlRewrite;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return \Magento\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite\Suffix
     */
    protected function _beforeSave()
    {
        $this->_coreUrlRewrite->validateSuffix($this->getValue());
        return $this;
    }
}
