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
namespace Magento\CatalogEvent\Model\Resource\Event\Grid;

class State implements \Magento\Core\Model\Option\ArrayInterface
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
            \Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE => __('Category Page'),
            \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE  => __('Product Page'),
        );
    }
}
