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
 * Controller that allows to display arbitrary page in design editor mode
 */
class Mage_DesignEditor_EditorController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_session;

    /**
     * Variable to store full action name
     *
     * @var string
     */
    protected $_fullActionName = '';

    /**
     * Enforce admin session with the active design editor mode
     *
     * @return Mage_DesignEditor_EditorController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->_session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$this->_session->isDesignEditorActive()) {
            Mage::getSingleton('Mage_Core_Model_Session')->addError(
                $this->__('Design editor is not initialized by administrator.')
            );
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        return $this;
    }

    /**
     * Display an arbitrary page by specified page type
     */
    public function pageAction()
    {
        try {
            $handle = $this->getRequest()->getParam('handle');

            // page type format
            if (!$handle || !preg_match('/^[a-z][a-z\d]*(_[a-z][a-z\d]*)*$/i', $handle)) {
                Mage::throwException($this->__('Invalid page handle specified.'));
            }

            // whether such page type exists
            if (!$this->getLayout()->getUpdate()->pageHandleExists($handle)) {
                Mage::throwException(
                    $this->__("Specified page type or page fragment type doesn't exist: '{$handle}'.")
                );
            }

            $this->_fullActionName = $handle;
            $this->addPageLayoutHandles();
            $this->loadLayoutUpdates();
            $this->generateLayoutXml();
            Mage::getModel('Mage_DesignEditor_Model_Layout')->sanitizeLayout($this->getLayout()->getNode());
            $this->generateLayoutBlocks();

            $blockHierarchy = $this->getLayout()->getBlock('design_editor_toolbar_handles_hierarchy');
            if ($blockHierarchy) {
                $blockHierarchy->setSelectedHandle($handle);
            }
            $blockBreadcrumbs = $this->getLayout()->getBlock('design_editor_toolbar_breadcrumbs');
            if ($blockBreadcrumbs) {
                $blockBreadcrumbs->setOmitCurrentPage(true);
            }

            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
            $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=UTF-8')->setHttpResponseCode(503);
        }
    }

    /**
     * Hack the "full action name" in order to render emulated layout
     *
     * @param string $delimiter
     * @return string
     */
    public function getFullActionName($delimiter = '_')
    {
        if ($this->_fullActionName) {
            return $this->_fullActionName;
        }
        return parent::getFullActionName($delimiter);
    }

    /**
     * Sets new skin for viewed store and returns customer back to the previous address
     */
    public function skinAction()
    {
        $skin = $this->getRequest()->get('skin');
        $backUrl = $this->_getRefererUrl();

        try {
            $this->_session->setSkin($skin);
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        }
        $this->getResponse()->setRedirect($backUrl);
    }

    /**
     * Compact history
     */
    public function compactHistoryAction()
    {
        $historyData = Mage::app()->getRequest()->getPost();

        if (!$historyData) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($this->__('Invalid post data')))
            ));
            return;
        }

        /** @var $historyModel Mage_DesignEditor_Model_History */
        $historyModel = Mage::getModel('Mage_DesignEditor_Model_History');
        try {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                Mage_Core_Model_Message::SUCCESS => array($historyModel->setChangeLog($historyData)->getCompactLog())
            )));
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($e->getMessage()))
            ));
        }
    }

    /**
     * Get layout xml
     */
    public function getLayoutUpdateAction()
    {
        $historyData = Mage::app()->getRequest()->getPost();

        if (!$historyData) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($this->__('Invalid post data')))
            ));
            return;
        }

        /** @var $historyModel Mage_DesignEditor_Model_History */
        $historyModel = Mage::getModel('Mage_DesignEditor_Model_History');
        try {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                Mage_Core_Model_Message::SUCCESS => array($historyModel->setChangeLog($historyData)->getCompactXml())
            )));
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
                array(Mage_Core_Model_Message::ERROR => array($e->getMessage()))
            ));
        }
    }
}
