<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml form container block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Widget_Form_Container extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_objectId = 'id';
    protected $_formScripts = array();
    protected $_formInitScripts = array();
    protected $_mode = 'edit';

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
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/' . $this->_controller . '_' . $this->_mode . '_form'));
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
        return Mage::getUrl('*/'.$this->_controller.'/save');
    }

    public function getFormHtml()
    {
        $this->getChild('form')->setData('action', $this->getSaveUrl());
        return $this->getChildHtml('form');
    }

    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

    public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderHtml()
    {
        return '<h3>' . $this->getHeaderText() . '</h3>';
    }

}
