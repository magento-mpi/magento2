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
        $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Themes'));

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
        $this->_title($this->__('New Theme'));
        $this->_forward('edit');
    }

    /**
     * Edit theme
     */
    public function editAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Design'))->_title($this->__('Themes'));

        $themeId  = (int) $this->getRequest()->getParam('id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');
        if ($themeId) {
            try {
                $theme->load($themeId);
            } catch (Exception $e){
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addError($this->__('The theme was not found.'));
                Mage::logException($e);
                $this->_redirect('*/*/');
                return;
            }
            if (!$theme->getId()) {
                $this->_getSession()->addError($this->__('The theme was not found.'));
                $this->_redirect('*/*/');
                return;
            }
            $this->_title($this->__('Edit Theme'));
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
        if ($this->getRequest()->getPost()) {
            $this->_redirect('*/*/');
        }
        $themeData = $this->getRequest()->getParam('theme');
        try {
            /** @var $theme Mage_Core_Model_Theme */
            $theme = Mage::getModel('Mage_Core_Model_Theme');
            if (isset($themeData['theme_id'])) {
                $theme->load($themeData['theme_id']);
            }
            $theme->setData($themeData)->save();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess($this->__('The theme has been saved.'));
        } catch (Exception $e){
            Mage::getSingleton('Mage_Adminhtml_Model_Session')
                ->addError($this->__('The theme was not saved'))
                ->setThemeData($themeData);
            Mage::logException($e);
            $this->_redirect('*/*/edit', array('id' => $theme->getId()));
            return;
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $themeId = $this->getRequest()->getParam('id');
        if (!$themeId) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            Mage::getModel('Mage_Core_Model_Theme')->load($themeId)->delete();
            Mage::getSingleton('Mage_Adminhtml_Model_Session')
                ->addSuccess($this->__('The theme has been deleted.'));
        } catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')
                ->addException($e, $this->__('Cannot delete the theme change.'));
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
