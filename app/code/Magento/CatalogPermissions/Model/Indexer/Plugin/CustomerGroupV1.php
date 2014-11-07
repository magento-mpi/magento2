<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class CustomerGroupV1
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
     * @param \Magento\Customer\Model\Resource\GroupRepository $subject
     * @param \Closure $proceed
     * @param \Magento\Customer\Model\Data\Group $customerGroup
     *
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        \Magento\Customer\Model\Resource\GroupRepository $subject,
        \Closure $proceed,
        \Magento\Customer\Model\Data\Group $customerGroup
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
     * @param \Magento\Customer\Model\Resource\GroupRepository $subject
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Customer\Model\Resource\GroupRepository $subject)
    {
        if ($this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }
        return true;
    }

    /**
     * Invalidate indexer on customer group delete
     *
     * @param \Magento\Customer\Model\Resource\GroupRepository $subject
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteById(\Magento\Customer\Model\Resource\GroupRepository $subject)
    {
        if ($this->appConfig->isEnabled()) {
            $this->getIndexer()->invalidate();
        }
        return true;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
        }
        return $this->indexer;
    }
}
