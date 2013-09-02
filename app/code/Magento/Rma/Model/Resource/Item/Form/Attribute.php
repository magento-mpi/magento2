<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA entity Form Attribute resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Item_Form_Attribute extends Magento_Eav_Model_Resource_Form_Attribute
{
    /**
     * Initialize connection and define main table
     */
    protected function _construct()
    {
        $this->_init('magento_rma_item_form_attribute', 'attribute_id');
    }
}
