<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Adminhtml Block
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Item_Attribute extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize rma item management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_rma_item_attribute';
        $this->_blockGroup = 'Enterprise_Rma';
        $this->_headerText = Mage::helper('Enterprise_Rma_Helper_Data')->__('Return Item Attribute');
        $this->_addButtonLabel = Mage::helper('Enterprise_Rma_Helper_Data')->__('Add New Attribute');
        parent::_construct();
    }
}
