<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller\Adminhtml\Wysiwyg\Images;

use Magento\Framework\App\Filesystem\DirectoryList;

class DeleteFiles extends \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images
{
    /**
     * Delete file from media storage
     *
     * @return void
     */
    public function execute()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                throw new \Exception('Wrong request.');
            }
            $files = $this->getRequest()->getParam('files');

            /** @var $helper \Magento\Cms\Helper\Wysiwyg\Images */
            $helper = $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            foreach ($files as $file) {
                $file = $helper->idDecode($file);
                /** @var \Magento\Framework\App\Filesystem $filesystem */
                $filesystem = $this->_objectManager->get('Magento\Framework\App\Filesystem');
                $dir = $filesystem->getDirectoryRead(DirectoryList::MEDIA_DIR);
                $filePath = $path . '/' . $file;
                if ($dir->isFile($dir->getRelativePath($filePath))) {
                    $this->getStorage()->deleteFile($filePath);
                }
            }
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result)
            );
        }
    }
}
