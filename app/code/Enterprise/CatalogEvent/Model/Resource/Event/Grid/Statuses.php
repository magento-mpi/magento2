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
 * Catalog Event statuses option array
 *
 * @category   Enterprise
 * @package    Enterpise_CatalogEvent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogEvent_Model_Resource_Event_Grid_Statuses implements Mage_Core_Model_Option_ArrayInterface
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
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Enterprise_CatalogEvent_Model_Event::STATUS_UPCOMING => __('Upcoming'),
            Enterprise_CatalogEvent_Model_Event::STATUS_OPEN 	  => __('Open'),
            Enterprise_CatalogEvent_Model_Event::STATUS_CLOSED   => __('Closed'),
        );
    }
}
