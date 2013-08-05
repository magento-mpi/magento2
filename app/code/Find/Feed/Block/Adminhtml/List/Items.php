<?php
/**
 * {license_notice}
 *
 * @category    
 * @package     _home
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TheFind feed product grid container
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container settings
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Find_Feed';
        $this->_controller = 'adminhtml_list_items';
        $this->_headerText = __('Product import');

        parent::_construct();

        $this->_removeButton('add');
    }
}
