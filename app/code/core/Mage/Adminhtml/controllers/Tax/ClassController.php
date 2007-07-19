<?php
class Mage_Adminhtml_Tax_ClassController extends Mage_Adminhtml_Controller_Action {

    public function saveAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            $class = Mage::getModel('tax/class');
            $class->setData($postData);

            try {
                $class->save();
                $classId = $class->getClassId();
                $classType = $class->getClassType();

                $this->_redirect("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}");
            } catch (Exception $e) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                # FIXME !!!!
            }
        }
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');

        $classType = strtolower($this->getRequest()->getParam('classType'));
        $classTypePhrase = ucfirst($classType);

        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax'), __('Tax Title'), Mage::getUrl('adminhtml/tax_rule'));
        $this->_addBreadcrumb(__("{$classTypePhrase} Tax Classes"), __("{$classTypePhrase} Tax Classes Title"), Mage::getUrl('adminhtml/tax_class_'.$classType));
        $this->_addBreadcrumb(__("Edit {$classTypePhrase} Tax Class"), __("Edit {$classTypePhrase} Tax Class Title"));

        $this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('adminhtml/session')->getMessages());

        $tabs = $this->getLayout()->createBlock('adminhtml/tax_tabs')
            ->setActiveTab('tax_class_' . strtolower($this->getRequest()->getParam('classType')));

        $this->_addLeft($tabs);
        $this->_addContent($this->getLayout()->createBlock('adminhtml/tax_class_page_edit'));

        $this->renderLayout();
    }

    public function deleteAction()
    {
        try {
            $classId = $this->getRequest()->getParam('classId');
            $classType = strtolower($this->getRequest()->getParam('classType'));

            $class = Mage::getSingleton('tax/class');
            $class->setClassId($classId);
            $class->delete();

            $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class_{$classType}"));
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            # FIXME !!!!
        }
    }

    public function saveGroupAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            $group = Mage::getModel('tax/class_group');
            $group->setData($postData);
            try {
                $group->save();
                $classId = $this->getRequest()->getParam('classId');
                $classType = $this->getRequest()->getParam('classType');
                $this->_redirect("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}");
            } catch ( Exception $e ) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                # FIXME !!!!
            }
        }
    }

    public function deleteGroupAction()
    {
        $groupId = $this->getRequest()->getParam('groupId');
        $classId = $this->getRequest()->getParam('classId');
        $classType = $this->getRequest()->getParam('classType');

        try {
            $group = Mage::getModel('tax/class_group');
            $group->setGroupId($groupId);
            $group->delete();
            $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}"));
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            # FIXME !!!!
        }
    }
}
