<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class GroupRepository
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $appConfig;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\CatalogPermissions\App\ConfigInterface $appConfig
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\CatalogPermissions\App\ConfigInterface $appConfig
    ) {
        $this->indexer = $indexer;
        $this->appConfig = $appConfig;
    }

    /**
     * Invalidate indexer on customer group save
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Customer\Api\Data\GroupInterface $customerGroup
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Customer\Api\GroupRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Customer\Api\Data\GroupInterface $customerGroup
    ) {
        $needInvalidating = !$customerGroup->getId();

        $customerGroupId = $proceed($customerGroup);

        if ($needInvalidating && $this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }

        return $customerGroupId;
    }

    /**
     * Invalidate indexer on customer group delete
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Customer\Api\GroupRepositoryInterface $subject)
    {
        return $this->invalidateIndexer();
    }

    /**
     * Invalidate indexer on customer group delete
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $subject
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteById(\Magento\Customer\Api\GroupRepositoryInterface $subject)
    {
        return $this->invalidateIndexer();
    }

    /**
     * Return own indexer object
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     *
     * @return bool
     */
    protected function invalidateIndexer()
    {
        if ($this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }
        return true;
    }
}
