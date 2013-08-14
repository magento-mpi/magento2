<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA entity Form Attribute resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Model_Resource_Item_Form_Attribute extends Magento_Eav_Model_Resource_Form_Attribute
{
    /**
     * Initialize connection and define main table
     */
    protected function _construct()
    {
        $this->_init('enterprise_rma_item_form_attribute', 'attribute_id');
    }
}
