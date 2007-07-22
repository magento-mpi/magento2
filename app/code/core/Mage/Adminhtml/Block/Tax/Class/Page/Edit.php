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
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/tax_class_grid_group', 'taxClassGrid'));
        $this->setChild('addForm', $this->getLayout()->createBlock("adminhtml/tax_class_{$classType}_form_add"));
        $this->setChild('renameForm', $this->getLayout()->createBlock("adminhtml/tax_class_form_rename"));
    }

    protected function _getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    protected function _getAddFormHtml()
    {
        return $this->getChildHtml('addForm');
    }

    protected function _getRenameFormHtml()
    {
        return $this->getChildHtml('renameForm');
    }

    protected function _getAddFormId()
    {
        return $this->getChild('addForm')->getForm()->getId();
    }

    protected function _getRenameFormId()
    {
        return $this->getChild('renameForm')->getForm()->getId();
    }
}