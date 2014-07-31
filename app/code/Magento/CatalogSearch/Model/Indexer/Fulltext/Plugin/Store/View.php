<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\Store;

use Magento\CatalogSearch\Model\Indexer\Fulltext\Plugin\AbstractPlugin;

class View extends AbstractPlugin
{
    /**
     * Invalidate indexer on store view save
     *
     * @param \Magento\Store\Model\Resource\Store $subject
     * @param \Closure $proceed
     * @param \Magento\Store\Model\Store $store
     *
     * @return \Magento\Store\Model\Resource\Store
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Store\Model\Resource\Store $subject,
        \Closure $proceed,
        \Magento\Store\Model\Store $store
    ) {
        $needInvalidation = $store->isObjectNew();
        $result = $proceed($store);
        if ($needInvalidation) {
            $this->getIndexer()->invalidate();
        }

        return $result;
    }

    /**
     * Invalidate indexer on store view delete
     *
     * @param \Magento\Store\Model\Resource\Store $subject
     * @param \Magento\Store\Model\Resource\Store $result
     *
     * @return \Magento\Store\Model\Resource\Store
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(
        \Magento\Store\Model\Resource\Store $subject,
        \Magento\Store\Model\Resource\Store $result
    ) {
        $this->getIndexer()->invalidate();

        return $result;
    }
}
