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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=>__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text',
            array(
                'label' => Mage::helper('catalog')->__('Name<br/>(For internal use)'),
                'name' => 'attribute_set_name',
                'required' => true,
                'class' => 'required-entry',
                'value' => $data->getAttributeSetName()
        ));

        if( !$this->getRequest()->getParam('id', false) ) {
            $fieldset->addField('gotoEdit', 'hidden',
                array(
                    'name' => 'gotoEdit',
                    'value' => '1'
            ));

            $sets = Mage::getModel('eav/entity_attribute_set')
                ->getResourceCollection()
                ->setEntityTypeFilter(Mage::registry('entityType'))
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select',
                array(
                    'label' => Mage::helper('catalog')->__('Based On'),
                    'name' => 'skeleton_set',
                    'required' => true,
                    'class' => 'required-entry',
                    'values' => $sets,
            ));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction(Mage::getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}