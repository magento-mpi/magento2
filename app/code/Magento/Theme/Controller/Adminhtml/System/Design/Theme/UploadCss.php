<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

class UploadCss extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * Upload css file
     *
     * @return void
     */
    public function execute()
    {
        /** @var $serviceModel \Magento\Theme\Model\Uploader\Service */
        $serviceModel = $this->_objectManager->get('Magento\Theme\Model\Uploader\Service');
        try {
            $cssFileContent = $serviceModel->uploadCssFile('css_file_uploader');
            $result = array('error' => false, 'content' => $cssFileContent['content']);
        } catch (\Magento\Framework\Model\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => __('We cannot upload the CSS file.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
        );
    }
}
