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
 * Product alerts newsletter queue edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_Adminhtml_Block_Alert_Queue_Edit extends Mage_Core_Block_Template
{
    protected  function _beforeToHtml() {

        $this->setTemplate('alert/queue/edit.phtml');

        $this->setChild('form',
            $this->getLayout()->createBlock('adminhtml/alert_queue_edit_form','form')
        );
        $queue = Mage::getSingleton('customeralert/queue');
        $queue->addTemplateData($queue);
        return parent::_beforeToHtml();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save',array('id'=>$this->_request->getParam('id')));
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Alert Template'),
                    'onclick'   => 'queueControl.save()',
                    'class'     => 'save'
                ))
        );

        $this->setChild('save_and_resume',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save And Resume'),
                    'onclick'   => 'queueControl.resume()',
                    'class'     => 'save'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Reset'),
                    'onclick'   => 'window.location = window.location'
                ))
        );

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => __('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class'     => 'back'
                    )
                )
        );

        $this->setChild('toggle_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label'   => __('Toggle Editor'),
                        'onclick' => 'queueControl.toggleEditor();'
                    )
                )
        );
        return parent::_prepareLayout();
    }

    public function getToggleButtonHtml()
    {
        return $this->getChildHtml('toggle_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getResumeButtonHtml()
    {
        return $this->getChildHtml('save_and_resume');
    }

    public function getIsPreview()
    {
        $queue = Mage::getSingleton('customeralert/queue');
        return !in_array($queue->getQueueStatus(), array(Mage_CustomerAlert_Model_Queue::STATUS_NEVER, Mage_Newsletter_Model_Queue::STATUS_PAUSE));
    }

    public function getIsTextType()
    {
        $queue = Mage::getSingleton('customeralert/queue');
        return $queue->getTemplate()->isPlain();
    }

    public function getCanResume()
    {
        $queue = Mage::getSingleton('customeralert/queue');
        return in_array($queue->getQueueStatus(), array(Mage_CustomerAlert_Model_Queue::STATUS_PAUSE));
    }

    public function getHeaderText()
    {
        return ( $this->getIsPreview() ? __('View Customer Alert') : __('Edit Customer Alert'));
    }


}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit END
