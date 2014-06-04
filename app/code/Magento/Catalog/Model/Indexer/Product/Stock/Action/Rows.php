<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Stock\Action;

/**
 * Class Rows reindex action for mass actions
 *
 * @package Magento\Catalog\Model\Indexer\Product\Stock\Action
 */
class Rows extends \Magento\Catalog\Model\Indexer\Product\Stock\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @throws \Magento\Catalog\Exception
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Catalog\Exception(__('Bad value was supplied.'));
        }
        $this->_logger->log('Rows reindex for products - ' . implode(",", $ids) . '');
    }
}
