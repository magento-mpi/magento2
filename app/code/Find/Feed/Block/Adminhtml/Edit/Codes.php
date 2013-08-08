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
 * Attribute map edit codes form container block
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_Edit_Codes extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form container
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Find_Feed';
        $this->_controller = 'adminhtml_edit_codes';

        parent::_construct();

        $this->_removeButton('back');
        $url = $this->getUrl('*/codes_grid/saveForm');
        $this->_updateButton('save', 'onclick', 'saveNewImportItem(\''.$url.'\')');
        $this->_updateButton('reset', 'label', 'Close');
        $this->_updateButton('reset', 'onclick', 'closeNewImportItem()');
    }

    /**
     * Return Form Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Import attribute map');
    }
}
