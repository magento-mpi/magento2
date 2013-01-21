<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fonts content block
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Fonts_Content extends Mage_Backend_Block_Widget_Container
{
    /**
     * Block construction
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_headerText = $this->__('Media Storage');
        $this->_removeButton('back')->_removeButton('edit');
        $this->_addButton('newfolder', array(
            'class'   => 'save',
            'label'   => $this->__('Create Folder...'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.newFolder();'
        ));

        $this->_addButton('delete_folder', array(
            'class'   => 'delete no-display',
            'label'   => $this->__('Delete Folder'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.deleteFolder();',
            'id'      => 'button_delete_folder'
        ));

        $this->_addButton('delete_files', array(
            'class'   => 'delete no-display',
            'label'   => $this->__('Delete File'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.deleteFiles();',
            'id'      => 'button_delete_files'
        ));

        $this->_addButton('insert_files', array(
            'class'   => 'save no-display',
            'label'   => $this->__('Insert File'),
            'type'    => 'button',
            'onclick' => 'MediabrowserInstance.insert();',
            'id'      => 'button_insert_files'
        ));
    }

    /**
     * Files action source URL
     *
     * @return string
     */
    public function getContentsUrl()
    {
        return $this->getUrl('*/*/contents', array('type' => $this->getRequest()->getParam('type')));
    }

    /**
     * Javascript setup object for filebrowser instance
     *
     * @return string
     */
    public function getFilebrowserSetupObject()
    {
        $setupObject = new Varien_Object();

        $setupObject->setData(array(
            'newFolderPrompt'                 => $this->__('New Folder Name:'),
            'deleteFolderConfirmationMessage' => $this->__('Are you sure you want to delete current folder?'),
            'deleteFileConfirmationMessage'   => $this->__('Are you sure you want to delete the selected file?'),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl'     => $this->getContentsUrl(),
            'onInsertUrl'     => $this->getOnInsertUrl(),
            'newFolderUrl'    => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl'  => $this->getDeleteFilesUrl(),
            'headerText'      => $this->getHeaderText()
        ));

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($setupObject);
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getNewfolderUrl()
    {
        return $this->getUrl('*/*/newFolder');
    }

    /**
     * Delete directory action target URL
     *
     * @return string
     */
    protected function getDeletefolderUrl()
    {
        return $this->getUrl('*/*/deleteFolder');
    }

    /**
     * Delete files action target URL
     *
     * @return string
     */
    public function getDeleteFilesUrl()
    {
        return $this->getUrl('*/*/deleteFiles');
    }

    /**
     * Insert file action target URL
     *
     * @return string
     */
    public function getOnInsertUrl()
    {
        return $this->getUrl('*/*/onInsert');
    }

    /**
     * Target element ID getter
     *
     * @return string
     */
    public function getTargetElementId()
    {
        return $this->getRequest()->getParam('target_element_id');
    }
}
