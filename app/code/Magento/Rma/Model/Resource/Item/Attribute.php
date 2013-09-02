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
 * RMA entity resource model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Model_Resource_Item_Attribute extends Magento_Eav_Model_Resource_Attribute
{
    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('magento_rma_item_eav_attribute_website');
    }

    /**
     * Get Form attribute table
     *
     * Get table, where dependency between form name and attribute ids is stored.
     *
     * @return string|null
     */
    protected function _getFormAttributeTable()
    {
        return $this->getTable('magento_rma_item_form_attribute');
    }

}
