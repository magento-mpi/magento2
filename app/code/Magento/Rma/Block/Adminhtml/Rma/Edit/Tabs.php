<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Adminhtml_Rma_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize RMA edit page tabs
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Return Information'));
    }
}
