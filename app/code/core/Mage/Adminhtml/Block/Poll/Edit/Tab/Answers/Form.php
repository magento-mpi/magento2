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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml poll edit answer tab form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Poll_Edit_Tab_Answers_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_answer_form', array('legend' => Mage::helper('poll')->__('Add New Answer')));

        $fieldset->addField('answer_title', 'text', array(
                    'name'      => 'answer_title',
                    'title'     => Mage::helper('poll')->__('Answer Title'),
                    'label'     => Mage::helper('poll')->__('Answer Title'),
                    'maxlength' => '255',
                    'no_span'   => true,
                )
        );

        $fieldset->addField('poll_id', 'hidden', array(
                    'name'      => 'poll_id',
                    'no_span'   => true,
                    'value'     => $this->getRequest()->getParam('id'),
                )
        );

        $fieldset->addField('add_button', 'note', array(
                    'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                                    ->setData(array(
                                        'label'     => Mage::helper('poll')->__('Add Answer'),
                                        'onclick'   => 'answers.add();',
                                        'class'     => 'add',
                                    ))->toHtml(),
                    'no_span'   => true,
                )
        );

        $this->setForm($form);
    }
}