<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class StoreGroup extends AbstractStore
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Core\Model\Store\Group $group */
        $group = $arguments[0];
        $needInvalidating = $group->dataHasChangedFor('root_category_id') && !$group->isObjectNew();
        $objectResource = $invocationChain->proceed($arguments);
        if ($needInvalidating) {
            $this->invalidatingIndexer();
        }

        return $objectResource;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundDelete(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Core\Model\Store\Group $group */
        $group = $arguments[0];
        $storeIds = $group->getStoreIds();
        $objectResource = $invocationChain->proceed($arguments);
        if (count($storeIds) > 0) {
            $this->cleanStoreData($storeIds);
        }

        return $objectResource;
    }
}
