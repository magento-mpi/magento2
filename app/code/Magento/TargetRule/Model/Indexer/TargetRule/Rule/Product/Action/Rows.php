<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action;

/**
 * Class Rows reindex action for mass actions
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action
 */
class Rows extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ruleIds
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($ruleIds)
    {
        if (empty($ruleIds)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for empty products array'));
        }
        try {
            $this->_reindexByRuleIds($ruleIds);
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
