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
 * Class Rows reindex action for mass actions
 *
 * @package Magento\TargetRule\Model\Indexer\Product\Rule\Action
 */
class Rows extends \Magento\TargetRule\Model\Indexer\Product\Rule\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for empty products array'));
        }
        try {
            $this->_reindexRows($ids);
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
