<?php
class Mage_Adminhtml_Tax_ClassController extends Mage_Adminhtml_Controller_Action {

    public function saveAction()
    {
        $classObject = new Varien_Object();

        $classObject->setClassGroupId($this->getRequest()->getParam('class_group'));

        $classObject->setClassId($this->getRequest()->getParam('class_id', null));
        $classObject->setClassName($this->getRequest()->getParam('class_name'));
        $classObject->setClassType($this->getRequest()->getParam('class_type'));

        $classId = Mage::getSingleton('tax/class')->save($classObject);
        $classType = $classObject->getClassType();

        $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}"));
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');

        $classType = strtolower($this->getRequest()->getParam('classType'));
        $classTypePhrase = ucfirst($classType);

        $this->_setActiveMenu('sales');
        $this->_addBreadcrumb(__('Tax rules'), __('Tax rules title'), Mage::getUrl('adminhtml/tax_rule'));
        $this->_addBreadcrumb(__("{$classTypePhrase} tax classes"), __("{$classTypePhrase} tax classes title"), Mage::getUrl('adminhtml/tax_class_'.$classType));
        $this->_addBreadcrumb(__("Edit {$classTypePhrase} tax class"), __("Edit {$classTypePhrase} tax class title"));

        $tabs = $this->getLayout()->createBlock('adminhtml/tax_tabs')
            ->setActiveTab('tax_class_' . strtolower($this->getRequest()->getParam('classType')));

        $grid = $this->getLayout()->createBlock('adminhtml/tax_class_grid_group', 'taxClassGrid');
        $form = $this->getLayout()->createBlock("adminhtml/tax_class_{$classType}_form_add");

        $this->_addLeft($tabs);
        $this->_addContent($grid);
        $this->_addContent($form);

        $this->renderLayout();
    }

    public function deleteAction()
    {
        $classId = $this->getRequest()->getParam('classId');
        $classType = strtolower($this->getRequest()->getParam('classType'));

        $classObject = new Varien_Object();
        $classObject->setClassId($classId);
        Mage::getSingleton('tax/class')->delete($classObject);

        $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class_{$classType}"));
    }

    public function saveGroupAction()
    {
        $groupObject = new Varien_Object();

        $groupObject->setClassGroupId($this->getRequest()->getParam('class_group'));
        $groupObject->setClassParentId($this->getRequest()->getParam('classId'));

        Mage::getSingleton('tax/class')->saveGroup($groupObject);

        $classId = $this->getRequest()->getParam('classId');
        $classType = $this->getRequest()->getParam('classType');

        $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}"));
    }

    public function deleteGroupAction()
    {
        $groupId = $this->getRequest()->getParam('groupId');
        $classId = $this->getRequest()->getParam('classId');
        $classType = $this->getRequest()->getParam('classType');

        Mage::getSingleton('tax/class')->deleteGroup($groupId);

        $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}"));
    }
}