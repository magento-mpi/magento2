<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Images manage controller for Cms WYSIWYG editor
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Cms_Wysiwyg_Images extends Magento_Adminhtml_Controller_Action
{
    /**
     * Init storage
     *
     * @return Magento_Adminhtml_Cms_Page_Wysiwyg_ImagesController
     */
    protected function _initAction()
    {
        $this->getStorage();
        return $this;
    }

    public function indexAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');

        try {
            $this->_objectManager->get('Magento_Cms_Helper_Wysiwyg_Images')->getCurrentPath();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_initAction()->loadLayout('overlay_popup');
        $block = $this->getLayout()->getBlock('wysiwyg_images.js');
        if ($block) {
            $block->setStoreId($storeId);
        }
        $this->renderLayout();
    }

    public function treeJsonAction()
    {
        try {
            $this->_initAction();
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Magento_Adminhtml_Block_Cms_Wysiwyg_Images_Tree')
                    ->getTreeJson()
            );
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    public function contentsAction()
    {
        try {
            $this->_initAction()->_saveSessionCurrentPath();
            $this->loadLayout('empty');
            $this->renderLayout();
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    public function newFolderAction()
    {
        try {
            $this->_initAction();
            $name = $this->getRequest()->getPost('name');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            $result = $this->getStorage()->createDirectory($name, $path);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    public function deleteFolderAction()
    {
        try {
            $path = $this->getStorage()->getSession()->getCurrentPath();
            $this->getStorage()->deleteDirectory($path);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Delete file from media storage
     *
     * @return void
     */
    public function deleteFilesAction()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                throw new Exception ('Wrong request.');
            }
            $files = $this->getRequest()->getParam('files');

            /** @var $helper Magento_Cms_Helper_Wysiwyg_Images */
            $helper = $this->_objectManager->get('Magento_Cms_Helper_Wysiwyg_Images');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            foreach ($files as $file) {
                $file = $helper->idDecode($file);
                $_filePath = $path . DS . $file;
                /** @var Magento_Filesystem $filesystem */
                $filesystem = $this->_objectManager->get('Magento_Filesystem');
                $filesystem->setWorkingDirectory($helper->getStorageRoot());
                if ($filesystem->isFile($_filePath)) {
                    $this->getStorage()->deleteFile($_filePath);
                }
            }
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Files upload processing
     */
    public function uploadAction()
    {
        try {
            $result = array();
            $this->_initAction();
            $targetPath = $this->getStorage()->getSession()->getCurrentPath();
            $result = $this->getStorage()->uploadFile($targetPath, $this->getRequest()->getParam('type'));
        } catch (Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));

    }

    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        $helper = $this->_objectManager->get('Magento_Cms_Helper_Wysiwyg_Images');
        $storeId = $this->getRequest()->getParam('store');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $helper->idDecode($filename);
        $asIs = $this->getRequest()->getParam('as_is');

        $this->_objectManager->get('Magento_Catalog_Helper_Data')->setStoreId($storeId);
        $helper->setStoreId($storeId);

        $image = $helper->getImageHtmlDeclaration($filename, $asIs);
        $this->getResponse()->setBody($image);
    }

    /**
     * Generate image thumbnail on the fly
     */
    public function thumbnailAction()
    {
        $file = $this->getRequest()->getParam('file');
        $file = $this->_objectManager->get('Magento_Cms_Helper_Wysiwyg_Images')->idDecode($file);
        $thumb = $this->getStorage()->resizeOnTheFly($file);
        if ($thumb !== false) {
            $image = $this->_objectManager->get('Magento_Core_Model_Image_AdapterFactory')->create();
            $image->open($thumb);
            $this->getResponse()->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } else {
            // todo: genearte some placeholder
        }
    }

    /**
     * Register storage model and return it
     *
     * @return Magento_Cms_Model_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        if (!Mage::registry('storage')) {
            $storage = Mage::getModel('Magento_Cms_Model_Wysiwyg_Images_Storage');
            Mage::register('storage', $storage);
        }
        return Mage::registry('storage');
    }

    /**
     * Save current path in session
     *
     * @return Magento_Adminhtml_Cms_Page_Wysiwyg_ImagesController
     */
    protected function _saveSessionCurrentPath()
    {
        $this->getStorage()
            ->getSession()
            ->setCurrentPath($this->_objectManager->get('Magento_Cms_Helper_Wysiwyg_Images')->getCurrentPath());
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::media_gallery');
    }
}
