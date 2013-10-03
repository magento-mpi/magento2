<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterpise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event statuses option array
 *
 * @category   Magento
 * @package    Enterpise_CatalogEvent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogEvent\Model\Resource\Event\Grid;

class Statuses implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\CatalogEvent\Model\Event::STATUS_UPCOMING => __('Upcoming'),
            \Magento\CatalogEvent\Model\Event::STATUS_OPEN 	  => __('Open'),
            \Magento\CatalogEvent\Model\Event::STATUS_CLOSED   => __('Closed'),
        );
    }
}
