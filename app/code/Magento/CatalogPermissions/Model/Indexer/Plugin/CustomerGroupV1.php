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
     * Invalidate indexer on customer group create
     *
     * @param \Magento\Customer\Service\V1\CustomerGroupService $subject
     * @param \Closure $proceed
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup $customerGroup
     *
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCreateGroup(
        \Magento\Customer\Service\V1\CustomerGroupService $subject,
        \Closure $proceed,
        \Magento\Customer\Service\V1\Data\CustomerGroup $customerGroup
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
     * @param \Magento\Customer\Service\V1\CustomerGroupService $subject
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteGroup(\Magento\Customer\Service\V1\CustomerGroupService $subject)
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
