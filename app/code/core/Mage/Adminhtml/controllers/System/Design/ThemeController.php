<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme controller
 */
class Mage_Adminhtml_System_Design_ThemeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system_design_theme');
        $this->renderLayout();
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create new theme
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit theme
     */
    public function editAction()
    {
        $themeId = (int) $this->getRequest()->getParam('id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        if ($themeId) {
            try {
                $theme->load($themeId);
                if (!$theme->getId()) {
                    Mage::throwException($this->__('The theme was not found.'));
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($this->__($e->getMessage()));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__('The theme was not found.'));
                Mage::logException($e);
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::register('theme', $theme);

        $this->loadLayout();
        $this->_setActiveMenu('Mage_Adminhtml::system_design_theme');
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        if (!$this->getRequest()->getPost()) {
            $this->_redirect('*/*/');
            return;
        }
        $themeData = $this->getRequest()->getParam('theme');
        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = Mage::getModel('Mage_Core_Model_Theme');
            if (isset($themeData['theme_id'])) {
                $theme->load($themeData['theme_id']);
            }
            $theme->setData($themeData)->save();
            $this->_getSession()->addSuccess($this->__('The theme has been saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError('The theme was not saved');
            Mage::logException($e);
            $this->_redirect('*/*/edit', array('id' => $theme->getId()));
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $themeId = $this->getRequest()->getParam('theme_id');
        if (!$themeId) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            Mage::getModel('Mage_Core_Model_Theme')->load($themeId)->delete();
            $this->_getSession()->addSuccess($this->__('The theme has been deleted.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__($e->getMessage()));
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot delete the theme.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to manage themes
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::theme');
    }
}
