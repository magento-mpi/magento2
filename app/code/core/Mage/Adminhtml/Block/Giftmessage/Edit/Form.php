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
 * Gift Message adminhtml edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Giftmessage_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('giftmessage', array('legend'=>$this->__('Gift Message')));

        $fieldset->addField('sender','text',
            array(
                'name'      =>  'sender',
                'required'  =>  true,
                'label'     =>  $this->__('From'),
                'class'     => 'required-entry'
            )
        );
        $fieldset->addField('recipient','text',
            array(
                'name'      =>  'recipient',
                'required'  =>  true,
                'label'     =>  $this->__('To'),
                'class'     => 'required-entry'
            )
        );

        $fieldset->addField('message','textarea',
            array(
                'name'      =>  'messagetext',
                'required'  =>  true,
                'label'     =>  $this->__('Message'),
                'class'     => 'required-entry'
            )
        );

        if(!$this->getParentBlock()->getMessage()->getSender()) {
            $this->getParentBlock()->getMessage()->setSender($this->getParentBlock()->getDefaultSender());
        }

        if(!$this->getParentBlock()->getMessage()->getRecipient()) {
            $this->getParentBlock()->getMessage()->setRecipient($this->getParentBlock()->getDefaultRecipient());
        }

        $form->setValues($this->getParentBlock()->getMessage()->getData());

        $this->setForm($form);
        return $this;
    }
} // Class Mage_Adminhtml_Block_Giftmessage_Edit_Form End