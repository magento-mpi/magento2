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
 * Ratings grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Rating_Rating extends Mage_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'rating';
        $this->_headerText = Mage::helper('Magento_Rating_Helper_Data')->__('Manage Ratings');
        $this->_addButtonLabel = Mage::helper('Magento_Rating_Helper_Data')->__('Add New Rating');
        parent::_construct();
    }
}
