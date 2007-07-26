<?php
/**
 * Admin CMS page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Page extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cms/page/form.phtml');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller'=>'cms_page', 'action'=>'save'));
    }

    protected function _beforeToHtml()
    {
        $this->assign('header', __('Manage Page'));
        return $this;
    }

    protected function _initChildren()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\'',
										'class'  => 'back'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Page'),
                    'onclick'   => 'tinyMCE.triggerSave();pageForm.submit();',
										'class'  => 'save'
                ))
        );

        $this->setChild('toggle_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Toggle Editor'),
                    'onclick'   => 'toggleEditor()',
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Page'),
                    'onclick'   => 'deleteConfirm(\''. __('Are you sure you want to do this?') .'\', \''.Mage::getUrl('*/*/delete/page/'. $this->getRequest()->getParam('page') .'').'\')',
										'class'  => 'delete'
                ))
        );
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getToggleButtonHtml()
    {
        return $this->getChildHtml('toggle_button');
    }

    public function getDeleteButtonHtml()
    {
        if( intval($this->getRequest()->getParam('page')) == 0 ) {
            return;
        }
        return $this->getChildHtml('delete_button');
    }
}
