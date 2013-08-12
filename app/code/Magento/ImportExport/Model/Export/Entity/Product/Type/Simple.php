<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export entity product type simple model
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Model_Export_Entity_Product_Type_Simple
    extends Magento_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = array(
        'has_options'      => array('source_model' => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean'),
        'required_options' => array('source_model' => 'Magento_Eav_Model_Entity_Attribute_Source_Boolean'),
        'created_at'       => array('backend_type' => 'datetime'),
        'updated_at'       => array('backend_type' => 'datetime')
    );

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = array(
        'old_id',
        'recurring_profile',
        'is_recurring',
        'tier_price',
        'group_price',
        'category_ids'
    );
}
