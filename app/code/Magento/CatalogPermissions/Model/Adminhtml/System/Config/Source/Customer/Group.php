<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source for customer group multiselect
 *
 * @category   Magento
 * @package    Magento_CatalogPermissions
 */
class Magento_CatalogPermissions_Model_Adminhtml_System_Config_Source_Customer_Group
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
