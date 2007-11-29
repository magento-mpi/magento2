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
 * Gift Message edit form admin
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Giftmessage_Edit extends Mage_Adminhtml_Block_Widget
{
    protected $_giftMessage = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('giftmessage/form.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('adminhtml/giftmessage_edit_form')
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                        'label' => $this->__('Save'),
                        'class' =>  'save',
                        'type'  => 'submit'
                    ))
        );

        $this->setChild('cancel_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                        'label' => $this->__('Cancel'),
                        'class' =>  'cancel listen-cancel',
                        'type'  => 'button'
                    ))
        );

        $this->setChild('remove_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                        'label' => $this->__('Remove'),
                        'class' =>  'delete listen-remove',
                        'type'  => 'button'
                    ))
        );

        $this->setChild('close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                        'label' => $this->__('Close'),
                        'type'  => 'button',
                        'onclick'  => 'giftMessageWindowObject.close()'
                    ))
        );

        return parent::_prepareLayout();
    }

    public function getFormHtml()
    {
        return $this->getChildHtml('form');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('cancel_button');
    }

    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getRemoveButtonHtml()
    {
        return $this->getChildHtml('remove_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->helper('giftmessage/url')->getAdminSaveUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type'),
                            $this->getRequest()->getParam('message'),
                            array('uniqueId'=>$this->getRequest()->getParam('uniqueId'))
        );
    }

    public function getEditUrl()
    {
        return $this->helper('giftmessage/url')->getAdminEditUrl(
                            $this->getRequest()->getParam('entity'),
                            $this->getRequest()->getParam('type')
        );
    }

    public function getButtonUrl()
    {
        return $this->helper('giftmessage/url')->getAdminButtonUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type')
        );
    }

    public function getRemoveUrl()
    {
        return $this->helper('giftmessage/url')->getAdminRemoveUrl(
                            $this->getRequest()->getParam('item'),
                            $this->getRequest()->getParam('type'),
                            array('uniqueId'=>$this->getRequest()->getParam('uniqueId'))
        );
    }

    protected function _initMessage()
    {
        $this->_giftMessage = $this->helper('giftmessage/message')->getGiftMessage(
                                            $this->getRequest()->getParam('message')
                              );
        return $this;
    }

    public function getMessage()
    {
        if(is_null($this->_giftMessage)) {
            $this->_initMessage();
        }

        return $this->_giftMessage;
    }

    public function getEscaped($value)
    {
        return $this->htmlEscape($value);
    }

    public function getEscapedForJs($value)
    {
        return addcslashes($value, "\\'\n\r\t");
    }

    public function getUniqueId()
    {
        return $this->getRequest()->getParam('uniqueId');
    }
} // Class Mage_Adminhtml_Block_Giftmessage_Edit End