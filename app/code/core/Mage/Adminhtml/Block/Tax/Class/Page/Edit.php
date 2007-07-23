<?php
/**
 * Admin tax class edit page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Class_Page_Edit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tax/class/page/edit.phtml');
    }

    protected function _initChildren()
    {
        $classType = strtolower($this->getRequest()->getParam('classType'));
        $this->setChild('renameForm', $this->getLayout()->createBlock("adminhtml/tax_class_form_rename"));

        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/tax_class_'. strtolower($this->getRequest()->getParam('classType')) ).'\''
                ))
        );

        $this->setChild('resetButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location.reload()'
                ))
        );

        $this->setChild('saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save class'),
                    'onclick'   => 'renameForm.submit();return false;'
                ))
        );

        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete class'),
                    'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . Mage::getUrl('*/*/delete', array('classId' => $this->getRequest()->getParam('classId'), 'classType' => $this->getRequest()->getParam('classType'))) . '\')',
                ))
        );
    }

    protected function _getRenameFormHtml()
    {
        return $this->getChildHtml('renameForm');
    }

    protected function _getRenameFormId()
    {
        return $this->getChild('renameForm')->getForm()->getId();
    }

    protected function _getHeader()
    {
        return __('Edit Class Details');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('backButton');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('resetButton');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        if( intval($this->getRequest()->getParam('classId')) == 0 ) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }
}