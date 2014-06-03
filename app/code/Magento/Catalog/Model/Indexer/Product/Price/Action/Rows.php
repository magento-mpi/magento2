<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

/**
 * Class Rows reindex action for mass actions
 *
 */
class Rows extends \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @return void
     * @throws \Magento\Catalog\Exception
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Catalog\Exception(__('Bad value was supplied.'));
        }
        try {
            $this->_reindexRows($ids);
        } catch (\Exception $e) {
            throw new \Magento\Catalog\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
