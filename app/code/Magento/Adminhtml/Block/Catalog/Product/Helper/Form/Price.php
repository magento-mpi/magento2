<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form price field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Helper_Form_Price extends Magento_Data_Form_Element_Text
{
    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param Magento_Tax_Helper_Data $taxData
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        Magento_Tax_Helper_Data $taxData,
        array $attributes = array()
    ) {
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
        $this->_taxData = $taxData;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addClass('validate-zero-or-greater');
    }

    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        /**
         * getEntityAttribute - use __call
         */
        $addJsObserver = false;
        if ($attribute = $this->getEntityAttribute()) {
            if (!($storeId = $attribute->getStoreId())) {
                $storeId = $this->getForm()->getDataObject()->getStoreId();
            }
            $store = Mage::app()->getStore($storeId);
            $html.= '<strong>' . Mage::app()->getLocale()->currency($store->getBaseCurrencyCode())->getSymbol() . '</strong>';
            if ($this->_taxData->priceIncludesTax($store)) {
                if ($attribute->getAttributeCode()!=='cost') {
                    $addJsObserver = true;
                    $html.= ' <strong>['.__('Inc. Tax').'<span id="dynamic-tax-'.$attribute->getAttributeCode().'"></span>]</strong>';
                }
            }
        }
        if ($addJsObserver) {
            $html .= $this->_getTaxObservingCode($attribute);
        }

        return $html;
    }

    protected function _getTaxObservingCode($attribute)
    {
        $spanId = "dynamic-tax-{$attribute->getAttributeCode()}";

        $html = "<script type='text/javascript'>if (dynamicTaxes == undefined) var dynamicTaxes = new Array(); dynamicTaxes[dynamicTaxes.length]='{$attribute->getAttributeCode()}'</script>";
        return $html;
    }

    public function getEscapedValue($index=null)
    {
        $value = $this->getValue();

        if (!is_numeric($value)) {
            return null;
        }

        return number_format($value, 2, null, '');
    }

}

