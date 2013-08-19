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
 * RMA items attributes edit page tabs
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Tabs
    extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rma_item_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
