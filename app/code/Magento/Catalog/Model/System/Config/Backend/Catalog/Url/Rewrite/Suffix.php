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
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\Core\Helper\Url\Rewrite $coreUrlRewrite
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\Core\Helper\Url\Rewrite $coreUrlRewrite,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreUrlRewrite = $coreUrlRewrite;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $this->_coreUrlRewrite->validateSuffix($this->getValue());
        return $this;
    }
}
