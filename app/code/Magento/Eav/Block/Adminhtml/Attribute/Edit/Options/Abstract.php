<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute add/edit form options tab
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract extends Magento_Core_Block_Abstract
{
    /**
     * Preparing layout, adding buttons
     *
     * @return Magento_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'labels',
            'Magento_Eav_Block_Adminhtml_Attribute_Edit_Options_Labels'
        );
        $this->addChild(
            'options',
            'Magento_Eav_Block_Adminhtml_Attribute_Edit_Options_Options'
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
