<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools;

class JsList extends \Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor\Tools
{
    /**
     * Ajax list of existing javascript files
     *
     * @return void
     */
    public function execute()
    {
        try {
            $themeContext = $this->_initContext();
            $editableTheme = $themeContext->getStagingTheme();
            $customization = $editableTheme->getCustomization();
            $customJsFiles = $customization->getFilesByType(\Magento\Framework\View\Design\Theme\Customization\File\Js::TYPE);
            $result = array('error' => false, 'files' => $customization->generateFileInfo($customJsFiles));
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
