<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml cms blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Cms_Block extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'cms_block';
        $this->_headerText = Mage::helper('Magento_Cms_Helper_Data')->__('Static Blocks');
        $this->_addButtonLabel = Mage::helper('Magento_Cms_Helper_Data')->__('Add New Block');
        parent::_construct();
    }

}
