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
 * Poll edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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