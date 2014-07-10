<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action;

/**
 * Class Rows reindex action for mass actions
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action
 */
class Rows extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $productIds
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($productIds)
    {
        if (empty($productIds)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for empty products array'));
        }
        try {
            $this->_reindexByProductIds($productIds);
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
