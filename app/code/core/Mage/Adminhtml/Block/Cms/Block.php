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
 * Adminhtml cms blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'cms_block';
        $this->_headerText = Mage::helper('Mage_Cms_Helper_Data')->__('Static Blocks');
        $this->_addButtonLabel = Mage::helper('Mage_Cms_Helper_Data')->__('Add New Block');
        parent::_construct();
    }

}
