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
     * Is true only for system (i.e. not user defined) attributes which use source model
     *
     * @return bool
     */
    public function canManageOptionDefaultOnly()
    {
        $attribute = $this->getAttributeObject();
        return !$attribute->getIsUserDefined() && $attribute->usesSource();
    }
}
