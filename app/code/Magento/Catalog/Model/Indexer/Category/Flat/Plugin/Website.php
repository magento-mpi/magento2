<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class Website extends AbstractStore
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundDelete(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\Core\Model\Website $website */
        $website = $arguments[0];
        $storeIds = $website->getStoreIds();
        $objectResource = $invocationChain->proceed($arguments);
        if (count($storeIds) > 0) {
            $this->cleanStoreData($storeIds);
        }

        return $objectResource;
    }
}
