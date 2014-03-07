<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer\Product;

/**
 * Class RefreshPlugin
 */
class RefreshPlugin
{
    /**
     * @var \Magento\PageCache\Model\Indexer\Context
     */
    protected $context;

    /**
     * @param \Magento\PageCache\Model\Indexer\Context $context
     */
    public function __construct(
        \Magento\PageCache\Model\Indexer\Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @param \Magento\Indexer\Model\ActionInterface $subject
     * @param array $arguments
     * @return array
     */
    public function beforeExecute(\Magento\Indexer\Model\ActionInterface $subject, $arguments)
    {
        $this->context->registerEntities(\Magento\Catalog\Model\Product::CACHE_TAG, $arguments);
        return $arguments;
    }
}
