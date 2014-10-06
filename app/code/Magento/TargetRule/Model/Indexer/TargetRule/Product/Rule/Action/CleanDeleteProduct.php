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
 * Class Clean deleted product action
 *
 * @package Magento\TargetRule\Model\Indexer\TargetRule\Rule\Product\Action
 */
class CleanDeleteProduct extends \Magento\TargetRule\Model\Indexer\TargetRule\AbstractAction
{
    /**
     * Remove deleted product from index
     *
     * @param int $productId
     * @throws \Magento\TargetRule\Exception
     *
     * @return void
     */
    public function execute($productId)
    {
        if (!isset($productId) || empty($productId)) {
            throw new \Magento\TargetRule\Exception(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->_deleteProductFromIndex(array($productId));
        } catch (\Exception $e) {
            throw new \Magento\TargetRule\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
