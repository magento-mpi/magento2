<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute add/edit form options tab
 *
 * @method Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options setReadOnly(bool $value)
 * @method null|bool getReadOnly
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
    /**
     * Retrieve option values collection
     * It is represented by an array in case of system attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return array|Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    protected function _getOptionValuesCollection(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($this->canManageOptionDefaultOnly()) {
            $options = Mage::getModel($attribute->getSourceModel())
                ->setAttribute($attribute)
                ->getAllOptions(true);
            return array_reverse($options);
        } else {
            parent::_getOptionValuesCollection($attribute);
        }
    }
}
