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
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files;

class Content
    extends \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content
{
    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('CSS Editor ') . __($this->helper('Magento\Theme\Helper\Storage')->getStorageTypeName());
    }
    /**
     * Javascript setup object for filebrowser instance
     *
     * @return string
     */
    public function getFilebrowserSetupObject()
    {
        $setupObject = new \Magento\Object();

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

        return $this->helper('Magento\Core\Helper\Data')->jsonEncode($setupObject);
    }
}
