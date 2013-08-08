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
 * TheFind feed attribute map grid container
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Codes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize grid container settings
     *
     */
    protected function _construct()
    {
        $this->_blockGroup      = 'Find_Feed';
        $this->_controller      = 'adminhtml_list_codes';
        $this->_headerText      = __('Attributes map');
        $this->_addButtonLabel  = __('Add new');

        parent::_construct();

        $url = $this->getUrl('*/codes_grid/editForm');
        $this->_updateButton('add', 'onclick', 'openNewImportWindow(\''.$url.'\');');
    }
}
