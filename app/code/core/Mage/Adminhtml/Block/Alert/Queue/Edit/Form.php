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
 * Adminhtml product alert queue edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_Adminhtml_Block_Alert_Queue_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $queue = Mage::getSingleton('customeralert/queue');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('customeralert')->__('Queue Information')));

        if($queue->getQueueStatus()==Mage_CustomerAlert_Model_Queue::STATUS_NEVER) {
            $fieldset->addField('date','date',array(
                'name'    =>    'start_at',
                'time'    =>    true,
                'label'   =>    Mage::helper('customeralert')->__('Queue Date Start'),
                'image'   =>    $this->getSkinUrl('images/grid-cal.gif'),
                'value'   =>    $queue->getQueueStartAt(),
                'title'   =>    Mage::helper('customeralert')->__('Queue Date Start')
            ));

            /*$fieldset->addField('stores','multiselect',array(
                'name'    =>    'stores[]',
                'time'    =>    true,
                'label'   =>    Mage::helper('customeralert')->__('Subscribers From'),
                'image'   =>    $this->getSkinUrl('images/grid-cal.gif'),
                'value'   =>    $queue->getQueueStores(),
                'title'   =>    Mage::helper('customeralert')->__('Subscribers From'),
                'values'  =>    Mage::getResourceSingleton('core/store_collection')->load()->toOptionArray(),
                'value'   =>    $queue->getStores(),
                'select_all' => Mage::helper('customeralert')->__('Select All Stores'),
                'deselect_all' => Mage::helper('customeralert')->__('Unselect All Stores'),
            ));*/
        } else {
            $fieldset->addField('date','date',array(
                'name'    =>    'start_at',
                'time'    =>    true,
                'disabled'=>    'true',
                'label'   =>    Mage::helper('customeralert')->__('Queue Date Start'),
                'image'   =>    $this->getSkinUrl('images/grid-cal.gif'),
                'value'   =>    $queue->getQueueStartAt(),
                'title'   =>    Mage::helper('customeralert')->__('Queue Date Start')
            ));

            /*$fieldset->addField('stores','multiselect',array(
                'name'    =>    'stores[]',
                'time'    =>    true,
                'label'   =>    Mage::helper('customeralert')->__('Subscribers From'),
                'image'   =>    $this->getSkinUrl('images/grid-cal.gif'),
                'value'   =>    $queue->getQueueStores(),
                'title'   =>    Mage::helper('customeralert')->__('Subscribers From'),
                'class'   =>  'required-entry',
                'values'  =>    Mage::getResourceSingleton('core/store_collection')->load()->toOptionArray(),
                'value'   =>    $queue->getStores(),
                'select_all' => Mage::helper('customeralert')->__('Select All Stores'),
                'deselect_all' => Mage::helper('customeralert')->__('Unselect All Stores')
            ));*/
        }

        $fieldset->addField('subject', 'text', array(
            'name'=>'subject',
            'label' => Mage::helper('customeralert')->__('Subject'),
            'title' => Mage::helper('customeralert')->__('Subject'),
            'class' => 'required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSubject()
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name'=>'sender_name',
            'label' => Mage::helper('customeralert')->__('Sender Name'),
            'title' => Mage::helper('customeralert')->__('Sender Name'),
            'class' => 'required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSenderName()
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name'=>'sender_email',
            'label' => Mage::helper('customeralert')->__('Sender Email'),
            'title' => Mage::helper('customeralert')->__('Sender Email'),
            'class' => 'validate-email required-entry',
            'required' => true,
            'value' => $queue->getTemplate()->getTemplateSenderEmail()
        ));

        $fieldset->addField('product', 'label', array(
            'name'=>'product',
            'label' => Mage::helper('customeralert')->__('Product'),
            'title' => Mage::helper('customeralert')->__('Product'),
            'value' => $queue->getProduct()->getName()
        ));

        if(in_array($queue->getQueueStatus(), array(Mage_CustomerAlert_Model_Queue::STATUS_NEVER, Mage_CustomerAlert_Model_Queue::STATUS_PAUSE))) {
            $fieldset->addField('text','editor', array(
                'name'    =>    'text',
                'wysiwyg' =>    !$queue->getTemplate()->isPlain(),
                'label'   =>    Mage::helper('customeralert')->__('Message'),
                'title'   =>    Mage::helper('customeralert')->__('Message'),
                'state'   =>    'html',
                'theme'   =>    'advanced',
                'class'   =>    'required-entry',
                'required'=>    true,
                'value'   =>    $queue->getTemplate()->getTemplateText(),
                'style'   => 'width:98%; height: 600px;',
            ));
        } else {
            $fieldset->addField('text','text', array(
                'name'    =>    'text',
                'label'   =>    Mage::helper('customeralert')->__('Message'),
                'title'   =>    Mage::helper('customeralert')->__('Message'),
                'value'   =>    $this->getUrl('*/alert_template/preview', array('_current'=>true))
            ));

            $form->getElement('text')->setRenderer(Mage::getModel('adminhtml/alert_renderer_text'));
            $form->getElement('subject')->setDisabled('true');
            $form->getElement('sender_name')->setDisabled('true');
            $form->getElement('sender_email')->setDisabled('true');
        }

    /*
        $form->getElement('template')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form_renderer_template')
        );
        */


        $this->setForm($form);
        return parent::_prepareForm();
    }
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form END
