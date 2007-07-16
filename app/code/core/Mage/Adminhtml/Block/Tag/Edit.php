<?php
/**
 * Tag edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Tag_Edit extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/tag/edit.phtml');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('saveButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => __('Save'),
                    'onclick' => 'tagForm.submit();return false;',
                ))
        );
        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => __('Delete'),
                    'onclick' => "window.location.href='" . $this->getDeleteUrl() . "'",
                ))
        );
        $this->setChild('tagForm',
            $this->getLayout()->createBlock('adminhtml/tag_edit_form')
        );
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('saveButton');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function getTagId()
    {
        return Mage::registry('tag')->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getHeader()
    {
        if (Mage::registry('tag')->getId()) {
            return __('Edit Tag ') . Mage::registry('tag')->getName();
        }
        else {
            return __('New Tag');
        }
    }
}
