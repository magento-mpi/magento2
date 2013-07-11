<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute add/edit form options tab
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract extends Mage_Core_Block_Abstract
{
    /**
     * Preparing layout, adding buttons
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'labels',
            'Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Labels'
        );
        $this->addChild(
            'options',
            'Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Options'
        );
        return parent::_prepareLayout();
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml();
    }
}
