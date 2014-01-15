<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Images manage controller for Cms WYSIWYG editor
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Controller\Adminhtml\Wysiwyg;

class Images extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init storage
     *
     * @return \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images
     */
    protected function _initAction()
    {
        $this->getStorage();
        return $this;
    }

    public function indexAction()
    {
        $storeId = (int)$this->getRequest()->getParam('store');

        try {
            $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images')->getCurrentPath();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_initAction();
        $this->_view->loadLayout('overlay_popup');
        $block = $this->_view->getLayout()->getBlock('wysiwyg_images.js');
        if ($block) {
            $block->setStoreId($storeId);
        }
        $this->_view->renderLayout();
    }

    public function treeJsonAction()
    {
        try {
            $this->_initAction();
            $this->getResponse()->setBody(
                $this->_view->getLayout()->createBlock('Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Tree')
                    ->getTreeJson()
            );
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    public function contentsAction()
    {
        try {
            $this->_initAction()->_saveSessionCurrentPath();
            $this->_view->loadLayout('empty');
            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
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
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    public function deleteFolderAction()
    {
        try {
            $path = $this->getStorage()->getSession()->getCurrentPath();
            $this->getStorage()->deleteDirectory($path);
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
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
            $helper = $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            foreach ($files as $file) {
                $file = $helper->idDecode($file);
                /** @var \Magento\Filesystem $filesystem */
                $filesystem = $this->_objectManager->get('Magento\Filesystem');
                $dir = $filesystem->getDirectoryRead(\Magento\Filesystem::MEDIA);
                $filePath = $path . '/' . $file;
                if ($dir->isFile($dir->getRelativePath($filePath))) {
                    $this->getStorage()->deleteFile($filePath);
                } 
            }
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }

    /**
     * Files upload processing
     */
    public function uploadAction()
    {
        try {
            $this->_initAction();
            $targetPath = $this->getStorage()->getSession()->getCurrentPath();
            $result = $this->getStorage()->uploadFile($targetPath, $this->getRequest()->getParam('type'));
        } catch (\Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));

    }

    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        $helper = $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images');
        $storeId = $this->getRequest()->getParam('store');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $helper->idDecode($filename);
        $asIs = $this->getRequest()->getParam('as_is');

        $this->_objectManager->get('Magento\Catalog\Helper\Data')->setStoreId($storeId);
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
        $file = $this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images')->idDecode($file);
        $thumb = $this->getStorage()->resizeOnTheFly($file);
        if ($thumb !== false) {
            /** @var \Magento\Image\Adapter\AdapterInterface $image */
            $image = $this->_objectManager->get('Magento\Image\AdapterFactory')->create();
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
        if (!$this->_coreRegistry->registry('storage')) {
            $storage = $this->_objectManager->create('Magento\Cms\Model\Wysiwyg\Images\Storage');
            $this->_coreRegistry->register('storage', $storage);
        }
        return $this->_coreRegistry->registry('storage');
    }

    /**
     * Save current path in session
     *
     * @return \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images
     */
    protected function _saveSessionCurrentPath()
    {
        $this->getStorage()
            ->getSession()
            ->setCurrentPath($this->_objectManager->get('Magento\Cms\Helper\Wysiwyg\Images')->getCurrentPath());
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
