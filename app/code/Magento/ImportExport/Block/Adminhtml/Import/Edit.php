<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import edit block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Block\Adminhtml\Import;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton(
            'back'
        )->removeButton(
            'reset'
        )->_updateButton(
            'save',
            'label',
            __('Check Data')
        )->_updateButton(
            'save',
            'id',
            'upload_button'
        )->_updateButton(
            'save',
            'onclick',
            'varienImport.postToFrame();'
        )->_updateButton(
            'save',
            'data_attribute',
            ''
        );

        $this->_objectId = 'import_id';
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
