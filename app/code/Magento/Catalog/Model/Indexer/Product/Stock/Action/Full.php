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
 * Class Full reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Product\Stock\Action
 */
class Full extends \Magento\Catalog\Model\Indexer\Product\Stock\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param null|array $ids
     * @throws \Magento\Catalog\Exception
     */
    public function execute($ids = null)
    {
        try {
            $this->_logger->log('Full reindex');
        } catch (\Exception $e) {
            throw new \Magento\Catalog\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
