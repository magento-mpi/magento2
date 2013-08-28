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
    public function __construct(Magento_Theme_Helper_Storage $themeStorage, Magento_Core_Helper_Data $coreData, Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($themeStorage, $coreData, $context, $data);
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('CSS Editor ') . __($this->_themeStorage->getStorageTypeName());
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

        return $this->_coreData->jsonEncode($setupObject);
    }
}
