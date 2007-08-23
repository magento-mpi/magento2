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

class Mage_Adminhtml_Block_Rating_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('rating_form', array('legend'=>__('Rating information')));
        $fieldset->addField('rating_code', 'text', array(
                                'label'     => __('Rating Title'),
                                'class'     => 'required-entry',
                                'required'  => true,
                                'name'      => 'rating_code',
                            )
        );

        if( Mage::getSingleton('adminhtml/session')->getRatingData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getRatingData());
            Mage::getSingleton('adminhtml/session')->setRatingData(null);
        } elseif ( Mage::registry('rating_data') ) {
            $form->setValues(Mage::registry('rating_data')->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}