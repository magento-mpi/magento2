<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle product attributes tab
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes
    extends Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    /**
     * Prepare attributes form of bundle product
     *
     * @return void
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $special_price = $this->getForm()->getElement('special_price');
        if ($special_price) {
            $special_price->setRenderer(
                $this->getLayout()
                    ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special')
                    ->setDisableChild(false)
            );
        }

        $sku = $this->getForm()->getElement('sku');
        if ($sku) {
            $sku->setRenderer(
                $this->getLayout()
                    ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend')
                    ->setDisableChild(false)
            );
        }

        $price = $this->getForm()->getElement('price');
        if ($price) {
            $price->setRenderer(
                $this->getLayout()
                    ->createBlock(
                        'Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend',
                        'adminhtml.catalog.product.bundle.edit.tab.attributes.price')
                    ->setDisableChild(true)
            );
        }

        $tax = $this->getForm()->getElement('tax_class_id');
        if ($tax) {
            $tax->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                //<![CDATA[
                function changeTaxClassId() {
                    if ($('price_type').value == '" . Magento_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . "') {
                        $('tax_class_id').disabled = true;
                        $('tax_class_id').value = '0';
                        $('tax_class_id').removeClassName('required-entry');
                        if ($('advice-required-entry-tax_class_id')) {
                            $('advice-required-entry-tax_class_id').remove();
                        }
                    } else {
                        $('tax_class_id').disabled = false;
                        " . ($tax->getRequired() ? "$('tax_class_id').addClassName('required-entry');" : '') . "
                    }
                }

                document.observe('dom:loaded', function() {
                    if ($('price_type')) {
                        $('price_type').observe('change', changeTaxClassId);
                        changeTaxClassId();
                    }
                });
                //]]>
                "
                . '</script>'
            );
        }

        $weight = $this->getForm()->getElement('weight');
        if ($weight) {
            $weight->setRenderer(
                $this->getLayout()
                    ->createBlock('Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend')
                    ->setDisableChild(true)
            );
        }

        $tier_price = $this->getForm()->getElement('tier_price');
        if ($tier_price) {
            $tier_price->setRenderer(
                $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier')
                    ->setPriceColumnHeader(__('Percent Discount'))
                    ->setPriceValidation('validate-greater-than-zero validate-number-range number-range-0.00-100.00')
            );
        }

        $groupPrice = $this->getForm()->getElement('group_price');
        if ($groupPrice) {
            $groupPrice->setRenderer(
                $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group')
                    ->setPriceColumnHeader(__('Percent Discount'))
                    ->setPriceValidation('validate-greater-than-zero validate-number-range number-range-0.00-100.00')
            );
        }

        $mapEnabled = $this->getForm()->getElement('msrp_enabled');
        if ($mapEnabled && $this->getCanEditPrice() !== false) {
            $mapEnabled->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                function changePriceTypeMap() {
                    if ($('price_type').value == " . Magento_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC . ") {
                        $('msrp_enabled').setValue("
                        . Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_NO
                        . ");
                        $('msrp_enabled').disable();
                        $('msrp_display_actual_price_type').setValue("
                        . Magento_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG
                        . ");
                        $('msrp_display_actual_price_type').disable();
                        $('msrp').setValue('');
                        $('msrp').disable();
                    } else {
                        $('msrp_enabled').enable();
                        $('msrp_display_actual_price_type').enable();
                        $('msrp').enable();
                    }
                }
                document.observe('dom:loaded', function() {
                    $('price_type').observe('change', changePriceTypeMap);
                    changePriceTypeMap();
                });
                "
                . '</script>'
            );
        }
    }

    /**
     * Get current product from registry
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')){
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }
}
