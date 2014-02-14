<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class CustomerGroup extends AbstractPlugin
{
    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\AbstractModel
     */
    public function aroundDelete(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $group = $invocationChain->proceed($arguments);
        $this->invalidateIndexer();
        return $group;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\AbstractModel
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $group = $invocationChain->proceed($arguments);
        $this->invalidateIndexer();
        return $group;
    }
}
