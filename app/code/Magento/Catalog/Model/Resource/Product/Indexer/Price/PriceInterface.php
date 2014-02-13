<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Product\Indexer\Price;

/**
 * Catalog Product Type Price Indexer interface
 *
 * @category    Magento
 * @package     Magento_Catalog
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

    /**
     * Register data required by product type process in event object
     *
     * @param \Magento\Index\Model\Event $event
     * @return void
     */
    public function registerEvent(\Magento\Index\Model\Event $event);
}
