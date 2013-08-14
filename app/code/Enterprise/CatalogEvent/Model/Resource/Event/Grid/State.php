<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterpise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event  statuses option array
 *
 * @category   Enterprise
 * @package    Enterpise_CatalogEvent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogEvent_Model_Resource_Event_Grid_State implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Enterprise_CatalogEvent_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_CatalogEvent_Helper_Data $catalogEventHelper
     */
    public function __construct(Enterprise_CatalogEvent_Helper_Data $catalogEventHelper)
    {
        $this->_helper = $catalogEventHelper;
    }

    /**
     * Return catalog event array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            0 => $this->_helper->__('Lister Block'),
            Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE => $this->_helper->__('Category Page'),
            Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE  => $this->_helper->__('Product Page'),
        );
    }
}
