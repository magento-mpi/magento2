<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Contyent Item Types Grid
 *
 * @category   Mage
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Types extends Mage_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_GoogleShopping';
        $this->_controller = 'adminhtml_types';
        $this->_addButtonLabel = Mage::helper('Magento_GoogleShopping_Helper_Data')->__('Add Attribute Mapping');
        $this->_headerText = Mage::helper('Magento_GoogleShopping_Helper_Data')->__('Manage Attribute Mapping');
        parent::_construct();
    }
}
