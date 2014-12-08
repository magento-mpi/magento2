<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event statuses option array
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogEvent\Model\Resource\Event\Grid;

class Statuses implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING => __('Upcoming'),
            \Magento\CatalogEvent\Model\Event::STATUS_OPEN => __('Open'),
            \Magento\CatalogEvent\Model\Event::STATUS_CLOSED => __('Closed')
        ];
    }
}
