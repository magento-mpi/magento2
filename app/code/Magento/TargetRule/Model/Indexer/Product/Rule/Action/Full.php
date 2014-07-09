<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\Product\Rule\Action;

/**
 * Class Full reindex action
 *
 * @package Magento\TargetRule\Model\Indexer\Product\Rule\Action
 */
class Full extends \Magento\TargetRule\Model\Indexer\Product\Rule\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param null|array $productIds
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($productIds = null)
    {
        try {
            $this->reindexAll();
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
