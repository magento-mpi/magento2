<?php
/**
 * Poll edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Poll_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('poll_form', array('legend'=>__('Poll information')));
        $fieldset->addField('poll_title', 'text', array(
                                'label'     => __('Poll Name'),
                                'class'     => 'required-entry',
                                'required'  => true,
                                'name'      => 'poll_title',
                            )
        );

        $fieldset->addField('active', 'select', array(
                                'label'     => __('Active'),
                                'name'      => 'active',
                                'values'    => array(
                                    array(
                                        'value'     => 1,
                                        'label'     => __('Yes'),
                                    ),

                                    array(
                                        'value'     => 0,
                                        'label'     => __('No'),
                                    ),
                                ),
                            )
        );

        $fieldset->addField('closed', 'select', array(
                                'label'     => __('Closed'),
                                'name'      => 'closed',
                                'values'    => array(
                                    array(
                                        'value'     => 1,
                                        'label'     => __('Yes'),
                                    ),

                                    array(
                                        'value'     => 0,
                                        'label'     => __('No'),
                                    ),
                                ),
                            )
        );

        if( Mage::registry('poll_data') ) {
            $form->setValues(Mage::registry('poll_data')->getData());

            $fieldset->addField('was_closed', 'hidden', array(
                                'name'      => 'was_closed',
                                'no_span'   => true,
                                'value'     => Mage::registry('poll_data')->getClosed()
                                )
            );
        } else {
            $form->getElement('active')->setValue(1);
        }
        $this->setForm($form);

        return parent::_prepareForm();
    }
}