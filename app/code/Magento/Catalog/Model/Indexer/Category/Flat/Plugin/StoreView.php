<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreView extends AbstractStore
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundSave(\Magento\Core\Model\Resource\Store $subject, \Closure $proceed, \Magento\Core\Model\AbstractModel $object)
    {
        /** @var \Magento\Core\Model\Store $store */
        $store = $arguments[0];
        $needInvalidating = $store->isObjectNew() || $store->dataHasChangedFor('group_id');
        $objectResource = $invocationChain->proceed($arguments);
        if ($needInvalidating) {
            $this->invalidateIndexer();
        }

        return $objectResource;
    }
}
