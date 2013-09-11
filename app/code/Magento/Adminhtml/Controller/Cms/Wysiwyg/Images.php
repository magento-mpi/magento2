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
namespace Magento\Adminhtml\Controller\Cms\Wysiwyg;

class Images extends \Magento\Adminhtml\Controller\Action
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
            \Mage::helper('Magento\Cms\Helper\Wysiwyg\Images')->getCurrentPath();
        } catch (\Exception $e) {
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
                $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Cms\Wysiwyg\Images\Tree')
                    ->getTreeJson()
            );
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    public function contentsAction()
    {
        try {
            $this->_initAction()->_saveSessionCurrentPath();
            $this->loadLayout('empty');
            $this->renderLayout();
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    public function newFolderAction()
    {
        try {
            $this->_initAction();
            $name = $this->getRequest()->getPost('name');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            $result = $this->getStorage()->createDirectory($name, $path);
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        }
        $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    public function deleteFolderAction()
    {
        try {
            $path = $this->getStorage()->getSession()->getCurrentPath();
            $this->getStorage()->deleteDirectory($path);
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
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
                throw new \Exception ('Wrong request.');
            }
            $files = $this->getRequest()->getParam('files');

            /** @var $helper \Magento\Cms\Helper\Wysiwyg\Images */
            $helper = \Mage::helper('Magento\Cms\Helper\Wysiwyg\Images');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            foreach ($files as $file) {
                $file = $helper->idDecode($file);
                $_filePath = $path . DS . $file;
                /** @var \Magento\Filesystem $filesystem */
                $filesystem = $this->_objectManager->get('Magento\Filesystem');
                $filesystem->setWorkingDirectory($helper->getStorageRoot());
                if ($filesystem->isFile($_filePath)) {
                    $this->getStorage()->deleteFile($_filePath);
                }
            }
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));
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
        } catch (\Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result));

    }

    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        $helper = \Mage::helper('Magento\Cms\Helper\Wysiwyg\Images');
        $storeId = $this->getRequest()->getParam('store');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $helper->idDecode($filename);
        $asIs = $this->getRequest()->getParam('as_is');

        \Mage::helper('Magento\Catalog\Helper\Data')->setStoreId($storeId);
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
        $file = \Mage::helper('Magento\Cms\Helper\Wysiwyg\Images')->idDecode($file);
        $thumb = $this->getStorage()->resizeOnTheFly($file);
        if ($thumb !== false) {
            $image = $this->_objectManager->get('Magento\Core\Model\Image\AdapterFactory')->create();
            $image->open($thumb);
            $this->getResponse()->setHeader('Content-Type', $image->getMimeType())->setBody($image->getImage());
        } else {
            // todo: genearte some placeholder
        }
    }

    /**
     * Register storage model and return it
     *
     * @return \Magento\Cms\Model\Wysiwyg\Images\Storage
     */
    public function getStorage()
    {
        if (!\Mage::registry('storage')) {
            $storage = \Mage::getModel('\Magento\Cms\Model\Wysiwyg\Images\Storage');
            \Mage::register('storage', $storage);
        }
        return \Mage::registry('storage');
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
            ->setCurrentPath(\Mage::helper('Magento\Cms\Helper\Wysiwyg\Images')->getCurrentPath());
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
