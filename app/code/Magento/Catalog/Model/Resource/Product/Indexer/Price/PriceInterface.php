<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Indexer\Price;

/**
 * Catalog Product Type Price Indexer interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface PriceInterface
{
    /**
     * Reindex temporary (price result data) for all products
     *
     * @return $this
     */
    public function reindexAll();

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     * @return $this
     */
    public function reindexEntity($entityIds);
}
