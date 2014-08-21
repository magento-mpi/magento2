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
 * Class Row reindex action
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action
 */
class Row extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $ruleId
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($ruleId = null)
    {
        if (!isset($ruleId) || empty($ruleId)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->_reindexByRuleId($ruleId);
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
