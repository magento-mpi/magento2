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
 * Files content block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content extends Magento_Backend_Block_Widget_Container
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
            'label'   => $this->__('Create Folder'),
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
        return $this->getUrl('*/*/contents', array('type' => $this->getRequest()->getParam('type'))
            + $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
    }

    /**
     * Javascript setup object for filebrowser instance
     *
     * @return string
     */
    public function getFilebrowserSetupObject()
    {
        $setupObject = new Magento_Object();

        $setupObject->setData(array(
            'newFolderPrompt'                 => $this->__('New Folder Name:'),
            'deleteFolderConfirmationMessage' => $this->__('Are you sure you want to delete this folder?'),
            'deleteFileConfirmationMessage'   => $this->__('Are you sure you want to delete this file?'),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl'     => $this->getContentsUrl(),
            'onInsertUrl'     => $this->getOnInsertUrl(),
            'newFolderUrl'    => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl'  => $this->getDeleteFilesUrl(),
            'headerText'      => $this->getHeaderText(),
            'showBreadcrumbs' => true
        ));

        return $this->helper('Magento_Core_Helper_Data')->jsonEncode($setupObject);
    }

    /**
     * New directory action target URL
     *
     * @return string
     */
    public function getNewfolderUrl()
    {
        return $this->getUrl('*/*/newFolder', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
    }

    /**
     * Delete directory action target URL
     *
     * @return string
     */
    protected function getDeletefolderUrl()
    {
        return $this->getUrl('*/*/deleteFolder', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
    }

    /**
     * Delete files action target URL
     *
     * @return string
     */
    public function getDeleteFilesUrl()
    {
        return $this->getUrl('*/*/deleteFiles', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
    }

    /**
     * Insert file action target URL
     *
     * @return string
     */
    public function getOnInsertUrl()
    {
        return $this->getUrl('*/*/onInsert', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams());
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
