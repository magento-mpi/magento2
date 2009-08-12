<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General
     */   
    protected function _prepareForm()
    {
        $model = Mage::registry('enterprise_customersegment_segment');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('segment_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('enterprise_customersegment')->__('General Properties')));

        if ($model->getId()) {
            $fieldset->addField('segment_id', 'hidden', array(
                'name' => 'segment_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('enterprise_customersegment')->__('Segment Name'),
            'title' => Mage::helper('enterprise_customersegment')->__('Segment Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('enterprise_customersegment')->__('Description'),
            'title' => Mage::helper('enterprise_customersegment')->__('Description'),
            'style' => 'width: 98%; height: 100px;',
        ));

        $fieldset->addField('processing_frequency', 'text', array(
            'name' => 'processing_frequency',
            'label' => Mage::helper('enterprise_customersegment')->__('Processing Frequency (days)'),
            'title' => Mage::helper('enterprise_customersegment')->__('Processing Frequency (days)'),
            'class' => 'validate-number',
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('enterprise_customersegment')->__('Status'),
            'title'     => Mage::helper('enterprise_customersegment')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('enterprise_customersegment')->__('Active'),
                '0' => Mage::helper('enterprise_customersegment')->__('Inactive'),
            ),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
