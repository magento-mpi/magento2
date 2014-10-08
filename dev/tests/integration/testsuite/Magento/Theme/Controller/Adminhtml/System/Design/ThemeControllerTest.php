<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @magentoAppArea adminhtml
 */
class ThemeControllerTest extends \Magento\Backend\Utility\Controller
{
    /** @var \Magento\Framework\App\Filesystem */
    protected $_filesystem;

    protected function setUp()
    {
        parent::setUp();

        $this->_filesystem = $this->_objectManager->get('Magento\Framework\App\Filesystem');
    }

    /**
     * Test upload JS file
     */
    public function testUploadJsAction()
    {
        $_FILES = array(
            'js_files_uploader' => array(
                'name' => 'simple-js-file.js',
                'type' => 'application/x-javascript',
                'tmp_name' => $this->_prepareFileForUploading(),
                'error' => '0',
                'size' => '28'
            )
        );

        /** @var \Magento\TestFramework\App\Filesystem $fileSystem */
        $fileSystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\App\Filesystem'
        );
        $fileSystem->overridePath(\Magento\Framework\App\Filesystem::SYS_TMP, '');

        $theme = $this->_objectManager->create('Magento\Framework\View\Design\ThemeInterface')
            ->getCollection()
            ->getFirstItem();

        $this->getRequest()->setPost('id', $theme->getId());
        $this->dispatch('backend/admin/system_design_theme/uploadjs');
        $output = $this->getResponse()->getBody();
        $this->assertContains('"error":false', $output);
        $this->assertContains('simple-js-file.js', $output);
    }

    /**
     * Prepare file for uploading
     *
     * @return string
     */
    protected function _prepareFileForUploading()
    {
        /**
         * Copy file to writable directory.
         * Uploader can copy(upload) and then remove this temporary file.
         */
        $fileName = __DIR__ . '/_files/simple-js-file.js';
        $varDir = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $rootDir = $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $destinationFilePath = 'simple-js-file.js';

        $rootDir->copyFile($rootDir->getRelativePath($fileName), $destinationFilePath, $varDir);

        return $varDir->getAbsolutePath($destinationFilePath);
    }
}
