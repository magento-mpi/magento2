<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import edit block
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Block_Adminhtml_Import_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', __('Check Data'))
            ->_updateButton('save', 'id', 'upload_button')
            ->_updateButton('save', 'onclick', 'varienImport.postToFrame();')
            ->_updateButton('save', 'data_attribute', '');

        $this->_objectId   = 'import_id';
        $this->_blockGroup = 'Magento_ImportExport';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Import');
    }
}
