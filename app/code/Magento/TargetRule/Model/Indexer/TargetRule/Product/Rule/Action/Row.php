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
 * Class Row reindex action
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Product\Rule\Action
 */
class Row extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $productId
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($productId = null)
    {
        if (!isset($productId) || empty($productId)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->_reindexByProductId($productId);
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
