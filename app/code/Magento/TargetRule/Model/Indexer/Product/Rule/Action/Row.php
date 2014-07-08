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
 * Class Row reindex action
 *
 * @package Magento\TargetRule\Model\Indexer\Product\Rule\Action
 */
class Row extends \Magento\TargetRule\Model\Indexer\Product\Rule\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $id
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->_reindexRows(array($id));
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
