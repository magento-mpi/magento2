<?php
/**
 * Admin tax rate save toolbar
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('createUrl', Mage::getUrl('adminhtml/tax_rate/save'));
        $this->setTemplate('tax/toolbar/rate/save.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('backButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\'',
					'class' => 'back'
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
                    'label'     => __('Save Rate'),
                    'onclick'   => 'wigetForm.submit();return false;',
					'class' => 'save'
                ))
        );

        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Rate'),
                    'onclick'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . Mage::getUrl('*/*/delete', array('rate' => $this->getRequest()->getParam('rate'))) . '\')',
					'class' => 'delete'
                ))
        );
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
        if( intval($this->getRequest()->getParam('rate')) == 0 ) {
            return;
        }
        return $this->getChildHtml('deleteButton');
    }
}