<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Theme;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\ResponseInterface;

class DownloadCss extends \Magento\Theme\Controller\Adminhtml\System\Design\Theme
{
    /**
     * Download css file
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        $file = $this->getRequest()->getParam('file');

        /** @var $helper \Magento\Core\Helper\Theme */
        $helper = $this->_objectManager->get('Magento\Core\Helper\Theme');
        $fileId = $helper->urlDecode($file);
        try {
            /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
            $theme = $this->_objectManager->create('Magento\Framework\View\Design\ThemeInterface')->load($themeId);
            if (!$theme->getId()) {
                throw new \InvalidArgumentException(sprintf('Theme not found: "%1".', $themeId));
            }
            $asset = $this->_assetRepo->createAsset($fileId, array('themeModel' => $theme));
            $relPath = $this->_appFileSystem->getDirectoryRead(DirectoryList::ROOT_DIR)
                ->getRelativePath($asset->getSourceFile());
            return $this->_fileFactory->create(
                $relPath,
                array(
                    'type'  => 'filename',
                    'value' => $relPath
                ),
                DirectoryList::ROOT_DIR
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('File not found: "%1".', $fileId));
            $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
