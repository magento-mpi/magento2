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
 * Quiq simple product creation
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Ivan Chepurnyi <ivan.chepurnoy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Simple extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setFieldNameSuffix('simple_product');

        $fieldset = $form->addFieldset('simple_product', array(
            'legend' => Mage::helper('catalog')->__('Quick simple product creation')
        ));

        $mainAttributesConfig = array(
            array('code' => 'name', 'autogenerate' => true ),
            array('code' => 'sku',  'autogenerate' => true ),
            array('code' => 'visibility' ),
            array('code' => 'status'     )
        );

        $attributes = $this->_getProduct()->getAttributes();

        foreach ($mainAttributesConfig as $attributeConfig) {
            $attribute = $attributes[$attributeConfig['code']];
            $inputType = $attribute->getFrontend()->getInputType();
            $element = $fieldset->addField(
                'simple_product_' . $attributeConfig['code'],
                 $inputType,
                 array(
                    'label'    => $attribute->getFrontend()->getLabel(),
                    'name'     => $attributeConfig['code'],
                    'required' => $attribute->getIsRequired(),
                 )
            )->setEntityAttribute($attribute);

            if (isset($attributeConfig['autogenerate'])) {
                $element->setDisabled('true');
                $element->setAfterElementHtml(
                     '<input type="checkbox" id="simple_product_' . $attributeConfig['code'] . '_autogenerate" '
                     . 'name="simple_product[' . $attributeConfig['code'] . '_autogenerate]" '
                     . 'onclick="toggleValueElements(this, this.parentNode)" checked/> '
                     . '<label for="simple_product_' . $attributeConfig['code'] . '_autogenerate" >'
                     . Mage::helper('catalog')->__('Autogenerate')
                     . '</label>'
                );
            }


            if ($inputType == 'select' || $inputType == 'multiselect') {
                $element->setValues($attribute->getFrontend()->getSelectOptions());
            }
        }


        foreach ($this->_getProduct()->getTypeInstance()->getConfigurableAttributes() as $attribute) {
            $fieldset->addField(
                'simple_product_' . $attribute->getProductAttribute()->getAttributeCode(),
                'select',
                array(
                    'label' => $attribute->getProductAttribute()->getFrontend()->getLabel(),
                    'name'  => $attribute->getProductAttribute()->getAttributeCode(),
                    'values' => $attribute->getProductAttribute()->getSource()->getAllOptions(),
                    'required' => true,
                    'class' => 'validate-configurable'
                )
            );
        }

        $fieldset->addField('simple_product_inventory_qty', 'text', array(
                'label' => Mage::helper('catalog')->__('Qty'),
                'name'  => 'stock_data[qty]',
                'class' => 'validate-number',
                'required' => true,
                'value'  => 0
        ));

        $fieldset->addField('simple_product_inventory_is_in_stock', 'select', array(
                'label' => Mage::helper('catalog')->__('Stock Availability'),
                'name'  => 'stock_data[is_in_stock]',
                'values' => array(
                    array('value'=>1, 'label'=> Mage::helper('catalog')->__('In Stock')),
                    array('value'=>0, 'label'=> Mage::helper('catalog')->__('Out of Stock'))
                ),
                'value' => 1
        ));

        $stockHiddenFields = array(
            'use_config_min_qty'            => 1,
            'use_config_min_sale_qty'       => 1,
            'use_config_max_sale_qty'       => 1,
            'use_config_backorders'         => 1,
            'use_config_notify_stock_qty'   => 1,
            'is_qty_decimal'                => 0
        );

        foreach ($stockHiddenFields as $fieldName=>$fieldValue) {
            $fieldset->addField('simple_product_inventory_' . $fieldName, 'hidden', array(
                'name'  => 'stock_data[' . $fieldName .']',
                'value' => $fieldValue
            ));
        }

        $headerBarHtml = $this->getButtonHtml(
            Mage::helper('catalog')->__('Quick Create'),
            'superProduct.quickCreateNewProduct()',
            'save'
        );

        $headerBarHtml .= $this->getButtonHtml(
            Mage::helper('catalog')->__('Create Empty'),
            'superProduct.createEmptyProduct()',
            'add'
        );

        if ($this->_getProduct()->getId()) {
            $headerBarHtml .= ' ' . $this->getButtonHtml(
                Mage::helper('catalog')->__('Create From Configurable'),
                'superProduct.createNewProduct()',
                'add'
            );
        }


        $fieldset->setHeaderBar(
            $headerBarHtml
        );

        $this->setForm($form);
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }
} // Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Simple End