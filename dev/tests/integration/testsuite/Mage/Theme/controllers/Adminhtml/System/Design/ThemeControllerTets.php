<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Theme
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Theme_Adminhtml_System_Design_ThemeControllerTest extends Mage_Backend_Utility_Controller
{
    /** @var Magento_Filesystem */
    protected $_filesystem;

    /** @var Mage_Core_Model_Dir */
    protected $_dirs;

    protected function setUp()
    {
        parent::setUp();

        $this->_filesystem = $this->_objectManager->get('Magento_Filesystem');
        $this->_dirs = $this->_objectManager->get('Mage_Core_Model_Dir');
    }

    /**
     * Test upload JS file
     */
    public function testUploadJsAction()
    {
        $_FILES = array(
            'js_files_uploader' => array(
                'name'     => 'simple-js-file.js',
                'type'     => 'application/x-javascript',
                'tmp_name' => $this->_prepareFileForUploading(),
                'error'    => '0',
                'size'     => '28'
            )
        );

        $theme = $this->_objectManager->create('Mage_Core_Model_Theme')->getCollection()->getFirstItem();

        $this->getRequest()->setPost('id', $theme->getId());
        $this->dispatch('backend/admin/system_design_theme/uploadjs');
        $output = $this->getResponse()->getBody();
        $this->assertContains('error:false', $output);
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
        $fileName = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'simple-js-file.js'));
        $varDir = $this->_dirs->getDir(Mage_Core_Model_Dir::VAR_DIR);
        $destinationFilePath = $varDir . DIRECTORY_SEPARATOR . 'simple-js-file.js';

        $this->_filesystem->copy($fileName, $destinationFilePath);
        $this->_filesystem->has($destinationFilePath);

        return $destinationFilePath;
    }
}
