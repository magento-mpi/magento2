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
class Magento_CatalogEvent_Model_Resource_Event_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_CatalogEvent_Model_Event::STATUS_UPCOMING => __('Upcoming'),
            Magento_CatalogEvent_Model_Event::STATUS_OPEN 	  => __('Open'),
            Magento_CatalogEvent_Model_Event::STATUS_CLOSED   => __('Closed'),
        );
    }
}
