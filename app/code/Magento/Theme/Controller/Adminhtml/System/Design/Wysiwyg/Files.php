<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files controller
 */
class Magento_Theme_Controller_Adminhtml_System_Design_Wysiwyg_Files extends Magento_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout('overlay_popup');
        $this->renderLayout();
    }

    /**
     * Tree json action
     */
    public function treeJsonAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Tree')
                    ->getTreeJson($this->_getStorage()->getTreeArray())
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode(array()));
        }
    }

    /**
     * New folder action
     */
    public function newFolderAction()
    {
        $name = $this->getRequest()->getPost('name');
        try {
            $path = $this->_getSession()->getStoragePath();
            $result = $this->_getStorage()->createFolder($name, $path);
        } catch (Magento_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => __('Sorry, there was an unknown error.'));
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Delete folder action
     */
    public function deleteFolderAction()
    {
        try {
            $path = $this->_getSession()->getStoragePath();
            $this->_getStorage()->deleteDirectory($path);
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Contents action
     */
    public function contentsAction()
    {
        try {
            $this->loadLayout('empty');
            $this->getLayout()->getBlock('wysiwyg_files.files')->setStorage($this->_getStorage());
            $this->renderLayout();

            $this->_getSession()->setStoragePath(
                $this->_objectManager->get('Magento_Theme_Helper_Storage')->getCurrentPath()
            );
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Files upload action
     */
    public function uploadAction()
    {
        try {
            $path = $this->_getSession()->getStoragePath();
            $result = $this->_getStorage()->uploadFile($path);
        } catch (Exception $e) {
            $result = array('error' => $e->getMessage(), 'errorcode' => $e->getCode());
        }
        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Preview image action
     */
    public function previewImageAction()
    {
        $file = $this->getRequest()->getParam('file');
        /** @var $helper Magento_Theme_Helper_Storage */
        $helper = $this->_objectManager->get('Magento_Theme_Helper_Storage');
        try {
            $this->_prepareDownloadResponse($file, array(
                'type'  => 'filename',
                'value' => $helper->getThumbnailPath($file)
            ));
        } catch (Exception $e) {
            $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            $this->_redirect('core/index/notfound');
        }
    }

    /**
     * Delete file from media storage
     * @throws Exception
     */
    public function deleteFilesAction()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                throw new Exception('Wrong request');
            }
            $files = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonDecode(
                $this->getRequest()->getParam('files')
            );
            foreach ($files as $file) {
                $this->_getStorage()->deleteFile($file);
            }
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
        }
    }

    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        /** @var $helperStorage Magento_Theme_Helper_Storage */
        $helperStorage = $this->_objectManager->get('Magento_Theme_Helper_Storage');
        $this->getResponse()->setBody($helperStorage->getRelativeUrl());
    }

    /**
     * Get storage
     *
     * @return Magento_Theme_Model_Wysiwyg_Storage
     */
    protected function _getStorage()
    {
        return $this->_objectManager->get('Magento_Theme_Model_Wysiwyg_Storage');
    }
}
