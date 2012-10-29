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
 * Poll manager grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Poll_Poll extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'poll';
        $this->_headerText = Mage::helper('Mage_Poll_Helper_Data')->__('Poll Manager');
        $this->_addButtonLabel = Mage::helper('Mage_Poll_Helper_Data')->__('Add New Poll');
        parent::_construct();
    }

}
