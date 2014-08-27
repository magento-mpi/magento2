<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url rewrite suffix backend
 */
namespace Magento\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite;

class Suffix extends \Magento\Framework\App\Config\Value
{
    /** @var \Magento\UrlRewrite\Helper\UrlRewrite */
    protected $urlRewriteHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
        $this->urlRewriteHelper = $urlRewriteHelper;
    }

    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $this->urlRewriteHelper->validateSuffix($this->getValue());
        return $this;
    }
}
