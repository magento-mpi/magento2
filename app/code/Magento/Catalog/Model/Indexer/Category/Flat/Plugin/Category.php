<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class Category
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Config
     */
    protected $config;

    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $indexer;

    /**
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
     * @param \Magento\Indexer\Model\Indexer $indexer
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Flat\Config $config,
        \Magento\Indexer\Model\Indexer $indexer
    ) {
        $this->config = $config;
        $this->indexer = $indexer;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\Indexer
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\Config::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Regenerate index for category on save if indexer does not use Mview
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Catalog\Model\Category
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $invocationChain->proceed($arguments);
        if ($this->config->isFlatEnabled()
            && $this->getIndexer()->getMode() == \Magento\Mview\View\StateInterface::MODE_DISABLED
        ) {
            $this->getIndexer()->reindexRow($category->getId());
        }
        return $category;
    }

    /**
     * Regenerate index for category and its parents on move if indexer does not use Mview
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Catalog\Model\Category
     */
    public function aroundMove(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $invocationChain->proceed($arguments);
        if ($this->config->isFlatEnabled()) {
            $affectedIds = $category->getAffectedCategoryIds();
            if (is_array($affectedIds)
                && $this->getIndexer()->getMode() == \Magento\Mview\View\StateInterface::MODE_DISABLED
            ) {
                $this->getIndexer()->reindexList($affectedIds);
            }
        }
        return $category;
    }
}
