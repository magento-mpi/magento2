<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance edit tabs container
 *
 * @category    Mage
 * @package     Mage_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('widget_instace_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Mage_Widget_Helper_Data')->__('Widget Instance'));
    }
}
