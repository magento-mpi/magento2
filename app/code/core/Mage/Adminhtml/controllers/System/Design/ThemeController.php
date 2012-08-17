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
        $this->_forward('edit');
    }

    /**
     * Edit theme
     *
     * @return Mage_Backend_Controller_ActionAbstract
     */
    public function editAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Design'))
            ->_title($this->__('Themes'));

        $id  = (int) $this->getRequest()->getParam('id');
        /** @var $theme Mage_Core_Model_Theme */
        $theme = Mage::getModel('Mage_Core_Model_Theme');

        if ($id) {
            $theme->load($id);

            if (!$theme->getId()) {
                $this->_getSession()->addError($this->__('Theme not found.'));
                return $this->_redirect('*/*/');
            }
            $this->_title($this->__('Edit Theme'));
        } else {
            $this->_title($this->__('New Theme'));
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
        if ($data = $this->getRequest()->getPost()) {
            $id = (int) $this->getRequest()->getParam('id');

            /** @var $theme Mage_Core_Model_Theme */
            $theme = Mage::getModel('Mage_Core_Model_Theme');
            if ($id) {
                $theme->load($id);
            }

            $theme->setData($data['theme']);
            if ($id) {
                $theme->setId($id);
            }
            try {
                $theme->save();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')->addSuccess($this->__('The theme has been saved.'));
            } catch (Exception $e){
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addError($e->getMessage())
                    ->setThemeData($data);
                $this->_redirect('*/*/edit', array('id'=>$theme->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $theme = Mage::getModel('Mage_Core_Model_Theme')->load($id);

            try {
                $theme->delete();

                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addSuccess($this->__('The theme has been deleted.'));
            } catch (Mage_Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                    ->addException($e, $this->__("Cannot delete the theme change."));
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::theme');
    }
}
