<?php
/**
 * Adminhtml container block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Container extends Mage_Core_Block_Template
{
    protected $_controller = 'empty';
    protected $_buttons = array(0 => array());
    protected $_headerText = 'Container Widget Header';

    protected function _addButton($id, $data, $level = 0)
    {
        if (!isset($this->_buttons[$level])) {
            $this->_buttons[$level] = array();
        }
        $this->_buttons[$level][$id] = $data;
        return $this;
    }

    protected function _removeButton($id)
    {
        foreach ($this->_buttons as $level => $buttons) {
            if (isset($buttons[$id])) {
                unset($this->_buttons[$level][$id]);
            }
        }
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
        return $this;
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $this->setChild($id . '_button', $this->getLayout()->createBlock('adminhtml/widget_button')->setData($data));
            }
        }
        return $this;
    }

    public function getButtonsHtml()
    {
        $out = '';
        foreach ($this->_buttons as $level => $buttons) {
            foreach ($buttons as $id => $data) {
                $out .= $this->getChildHtml($id . '_button');
            }
        }
        return $out;
    }

    public function getHeaderText()
    {
        return $this->_headerText;
    }

    public function getHeaderCssClass()
    {
        return 'head-' . strtr($this->_controller, '_', '-');
    }

}
