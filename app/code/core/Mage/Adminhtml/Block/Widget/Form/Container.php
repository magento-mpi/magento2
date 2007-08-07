<?php
/**
 * Adminhtml form container block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';
    protected $_formScripts = array();

    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('widget/form/container.phtml');

        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\'' . $this->getBackUrl() . '\'',
            'class'     => 'back',
        ));
        $this->_addButton('reset', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location.href = window.location.href',
        ));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (! empty($objId)) {
            $this->_addButton('delete', array(
                'label'     => __('Delete'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. __('Are you sure you want to do this?') .'\', \'' . $this->getDeleteUrl() . '\')',
            ));
        }

        $this->_addButton('save', array(
            'label'     => __('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ));
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_edit_form'));
        return $this;
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/');
    }

    public function getDeleteUrl()
    {
        return Mage::getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller' => $this->_controller, 'action' => 'save'));
    }

    public function getFormHtml()
    {
        $this->getChild('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

}
