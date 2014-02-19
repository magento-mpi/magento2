<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Controller\Adminhtml\System\Design\Editor;

/**
 * Files controller
 */
class Files
    extends \Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files
{
    /**
     * Tree json action
     *
     * @return void
     */
    public function treeJsonAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->_view->getLayout()->createBlock('Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Files\Tree')
                    ->getTreeJson($this->_getStorage()->getTreeArray())
            );
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Logger')->logException($e);
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode(array()));
        }
    }

    /**
     * Contents action
     *
     * @return void
     */
    public function contentsAction()
    {
        try {
            $this->_view->loadLayout('empty');
            $this->_view->getLayout()->getBlock('editor_files.files')->setStorage($this->_getStorage());
            $this->_view->renderLayout();

            $this->_getSession()->setStoragePath(
                $this->_objectManager->get('Magento\Theme\Helper\Storage')->getCurrentPath()
            );
        } catch (\Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
        }
    }
}
