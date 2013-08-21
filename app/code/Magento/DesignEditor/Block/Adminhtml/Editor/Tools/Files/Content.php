<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files content block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Content
    extends Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Content
{
    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('CSS Editor ') . __($this->helper('Magento_Theme_Helper_Storage')->getStorageTypeName());
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
            'newFolderPrompt'                 => __('New Folder Name:'),
            'deleteFolderConfirmationMessage' => __('Are you sure you want to delete this folder?'),
            'deleteFileConfirmationMessage'   => __('Are you sure you want to delete this file?'),
            'targetElementId' => $this->getTargetElementId(),
            'contentsUrl'     => $this->getContentsUrl(),
            'onInsertUrl'     => $this->getOnInsertUrl(),
            'newFolderUrl'    => $this->getNewfolderUrl(),
            'deleteFolderUrl' => $this->getDeletefolderUrl(),
            'deleteFilesUrl'  => $this->getDeleteFilesUrl(),
            'headerText'      => $this->getHeaderText(),
            'showBreadcrumbs' => false
        ));

        return $this->helper('Magento_Core_Helper_Data')->jsonEncode($setupObject);
    }
}
