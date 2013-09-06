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
 * Catalog Event  statuses option array
 *
 * @category   Magento
 * @package    Enterpise_CatalogEvent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogEvent_Model_Resource_Event_Grid_State implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return catalog event array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            0 => __('Lister Block'),
            Magento_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => __('Category Page'),
            Magento_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE  => __('Product Page'),
        );
    }
}
