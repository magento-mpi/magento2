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
 * Class Row reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Product\Stock\Action
 */
class Row extends \Magento\Catalog\Model\Indexer\Product\Stock\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $id
     * @throws \Magento\Catalog\Exception
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Catalog\Exception(__('Could not rebuild index for undefined product'));
        }
        $this->_logger->log('Row reindex for product - ' . $id . '');
    }
}
