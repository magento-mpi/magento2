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

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Core_Block_Template
{
    protected $_controller = 'empty';
    protected $_buttons = array(0=>array());
    protected $_objectId = 'id';
    protected $_formScripts = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('widget/form/container.phtml');
        $this->_init();
    }

    protected function _init()
    {
        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'window.location.href=\'' . Mage::getUrl('*/*/') . '\'',
            'class'     => 'back',
        ));
        $this->_addButton('reset', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location.href = window.location.href',
        ));
        $this->_addButton('delete', array(
            'label'     => __('Delete'),
            'class'     => 'delete',
        ));
        $this->_addButton('save', array(
            'label'     => __('Save'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'save',
        ));
        return $this;
    }

    protected function _addButton($id, $data, $level = 0)
    {
        if (!isset($this->_buttons[$level])) {
            $this->_buttons[$level] = array();
        }
        $this->_buttons[$level][$id] = $data;
        return $this;
    }

    protected function _updateButton($id, $key=null, $data)
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                if (!empty($key)) {
                    $this->_buttons[$level][$id][$key] = $data;
                } else {
                    $this->_buttons[$level][$id] = $data;
                }
                break;
            }
        }
    }

    protected function _getDeleteButtonClick($button=null)
    {
        if (!empty($button) && is_array($button) && !empty($button['onclick'])) {
            return $button['onclick'];
        }
        $onclick = 'deleteConfirm(\''. __('Are you sure you want to do this?') .'\', \''.Mage::getUrl('*/*/delete/' . $this->_objectId . '/'. $this->getRequest()->getParam($this->_objectId) .'').'\')';
        return $onclick;
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml', array('controller' => $this->_controller, 'action' => 'save'));
    }

    protected function _initChildren()
    {
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                if ('delete' == $id) {
                    $data['onclick'] = $this->_getDeleteButtonClick($data);
                }
                $this->setChild($id . '_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data));
            }
        }
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_edit_form'));
    }

    public function getButtonsHtml()
    {
        $out = '';
        $objId = $this->getRequest()->getParam($this->_objectId);
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                if (! (('delete' == $id) && empty($objId)) ) {
                    $out .= $this->getChildHtml($id . '_button');
                }
            }
        }
        return $out;
    }

    public function getHeaderText()
    {
        return __('New Object');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
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
