<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Google Contyent Item Types Grid
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Types extends Mage_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_GoogleShopping';
        $this->_controller = 'adminhtml_types';
        $this->_addButtonLabel = __('Add Attribute Mapping');
        $this->_headerText = __('Manage Attribute Mapping');
        parent::_construct();
    }
}
