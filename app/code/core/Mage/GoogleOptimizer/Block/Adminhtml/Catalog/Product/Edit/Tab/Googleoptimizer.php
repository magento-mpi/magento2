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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer Tab
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Googleoptimizer_Block_Adminhtml_Catalog_Product_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Catalog_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

//        $form->setDataObject($this->getProduct());

        $fieldset = $form->addFieldset('googleoptimizer_fields',
            array('legend'=>Mage::helper('googleoptimizer')->__('Google Optimizer Scripts'))
        );

        $disabledScriptsFields = false;
        $values = array();
        if ($this->getGoogleOptimizer() && $this->getGoogleOptimizer()->getData()) {
            $disabledScriptsFields = true;
            $values = $this->getGoogleOptimizer()->getData();
            $checkedUseDefault = true;
            if ($this->getGoogleOptimizer()->getStoreId() == $this->getProduct()->getStoreId()) {
                $checkedUseDefault = false;
                $disabledScriptsFields = false;
                $fieldset->addField('code_id', 'hidden', array('name' => 'code_id'));
            }
            // show 'use default' checkbox if store different for default and product has scripts for default store
            if ($this->getProduct()->getStoreId() != '0') {
                $fieldset->addField('store_flag', 'checkbox',
                    array(
                        'name'  => 'store_flag',
                        'value' => '1',
                        'label' => Mage::helper('googleoptimizer')->__('Use Default Values'),
                        'class' => 'checkbox',
                        'required' => false,
                        'onchange' => 'googleOptimizerScopeAction()',
                    )
                )->setIsChecked($checkedUseDefault);
            }
        }

        $fieldset->addField('export_controls', 'text', array('name'  => 'export_controls'));

        $fieldset->addField('control_script', 'textarea',
            array(
                'name'  => 'control_script',
                'label' => Mage::helper('googleoptimizer')->__('Control Script'),
                'class' => 'textarea googleoptimizer',
                'required' => false,
            )
        );
        $fieldset->addField('tracking_script', 'textarea',
            array(
                'name'  => 'tracking_script',
                'label' => Mage::helper('googleoptimizer')->__('Tracking Script'),
                'class' => 'textarea googleoptimizer',
                'required' => false,
            )
        );
        $fieldset->addField('conversion_script', 'textarea',
            array(
                'name'  => 'conversion_script',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Script'),
                'class' => 'textarea googleoptimizer',
                'required' => false,
            )
        );

        $fieldset->addField('conversion_page', 'select',
            array(
                'name'  => 'conversion_page',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Page'),
                'values'=> Mage::getModel('googleoptimizer/adminhtml_system_config_source_googleoptimizer_conversionpages')->toOptionArray(),
                'class' => 'select googleoptimizer',
                'required' => false,
                'onchange' => 'googleOptimizerConversionPageAction(this)'
            )
        );

        $fieldset->addField('conversion_page_url', 'text',
            array(
                'name'  => 'conversion_page_url',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Page URL'),
                'class' => 'input-text',
                'readonly' => true,
                'required' => false,
                'note' => Mage::helper('googleoptimizer')->__('Please copy and paste this value to experiment edit form')
            )
        );

        if (Mage::helper('googleoptimizer')->getConversionPagesUrl()
            && $this->getGoogleOptimizer()
            && $this->getGoogleOptimizer()->getConversionPage())
        {
            $form->getElement('conversion_page_url')
                ->setValue(Mage::helper('googleoptimizer')
                    ->getConversionPagesUrl()->getData($this->getGoogleOptimizer()->getConversionPage())
                );
        }

        if ($disabledScriptsFields) {
            foreach ($fieldset->getElements() as $element) {
                if ($element->getType() == 'textarea' || $element->getType() == 'select') {
                    $element->setDisabled($disabledScriptsFields);
                }
            }
            $form->getElement('export_controls')->setDisabled($disabledScriptsFields);
        }

        $fakeEntityAttribute = Mage::getModel('catalog/resource_eav_attribute');

        foreach ($fieldset->getElements() as $element) {
            if ($element->getId() != 'store_flag') {
                $element->setEntityAttribute($fakeEntityAttribute);
            }
        }

        $form->getElement('export_controls')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_googleoptimizer_import')
        );

        $form->addValues($values);
        $form->setFieldNameSuffix('googleoptimizer');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getProduct()
    {
        return Mage::registry('product');
    }

    public function getGoogleOptimizer()
    {
        return $this->getProduct()->getGoogleOptimizerCodes();
    }

    public function getTabLabel()
    {
        return Mage::helper('googleoptimizer')->__('Product View Optimization');
    }

    public function getTabTitle()
    {
        return Mage::helper('googleoptimizer')->__('Product View Optimization');
    }

    public function canShowTab()
    {
        if ($this->getProduct()->getAttributeSetId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        return false;
    }

}
