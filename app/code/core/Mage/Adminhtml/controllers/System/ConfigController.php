<?php
/**
 * config controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_System_ConfigController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setFlag('index', 'no-preDispatch', true);
        return parent::_construct();
    }

    public function indexAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('system/config');

        $this->_addBreadcrumb(__('System'), __('System Title'), Mage::getUrl('adminhtml/system'));

        $this->getLayout()->getBlock('left')
            ->append($this->getLayout()->createBlock('adminhtml/system_config_tabs')->initTabs());

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_config_edit')->initForm());

        $this->renderLayout();
    }

    public function saveAction()
    {
        Mage::getResourceModel('adminhtml/config')->saveSectionPost(
            $this->getRequest()->getParam('section'),
            $this->getRequest()->getParam('website'),
            $this->getRequest()->getParam('store'),
            $this->getRequest()->getPost('groups')
        );
        $this->_redirect('*/*/edit', array('_current'=>array('section', 'website', 'store')));
    }    
}
