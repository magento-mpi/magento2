<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer\Category;

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
     * @param array $ids
     * @return array
     */
    public function beforeExecute(\Magento\Indexer\Model\ActionInterface $subject, $ids)
    {
        $this->context->registerEntities(\Magento\Catalog\Model\Category::CACHE_TAG, $ids);
        return array($ids);
    }
}
