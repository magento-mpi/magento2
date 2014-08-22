<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule\Action;

/**
 * Class Clean index action
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action
 */
class Clean extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Execute clean index
     *
     * @param null|array $ids
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($ids = null)
    {
        try {
            $this->_cleanAll();
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
