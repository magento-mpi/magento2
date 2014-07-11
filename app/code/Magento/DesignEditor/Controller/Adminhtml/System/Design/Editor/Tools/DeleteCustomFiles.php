<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools;

class DeleteCustomFiles extends \Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools
{
    /**
     * Delete custom file action
     *
     * @return void
     */
    public function execute()
    {
        $removeJsFiles = (array)$this->getRequest()->getParam('js_removed_files');
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $editableTheme->getCustomization()->delete($removeJsFiles);
            $this->_forward('jsList');
        } catch (\Exception $e) {
            $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
