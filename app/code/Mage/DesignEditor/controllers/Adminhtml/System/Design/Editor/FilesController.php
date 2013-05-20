<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files controller
 */

require_once  'Mage/Theme/controllers/Adminhtml/System/Design/Wysiwyg/FilesController.php';

class Mage_DesignEditor_Adminhtml_System_Design_Editor_FilesController extends Mage_Theme_Adminhtml_System_Design_Wysiwyg_FilesController
{
    /**
     * Tree json action
     */
    public function treeJsonAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Files_Tree')
                    ->getTreeJson($this->_getStorage()->getTreeArray())
            );
        } catch (Exception $e) {
            $this->_objectManager->get('Mage_Core_Model_Logger')->logException($e);
            $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode(array()));
        }
    }

    /**
     * Contents action
     */
    public function contentsAction()
    {
        try {
            $this->loadLayout('empty');
            $this->getLayout()->getBlock('editor_files.files')->setStorage($this->_getStorage());
            $this->renderLayout();

            $this->_getSession()->setStoragePath(
                $this->_objectManager->get('Mage_Theme_Helper_Storage')->getCurrentPath()
            );
        } catch (Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Mage_Core_Helper_Data')->jsonEncode($result));
        }
    }
}
