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
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Price extends \Magento\Data\Form\Element\Text
{
    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Tax\Helper\Data $taxData
     * @param array $attributes
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Tax\Helper\Data $taxData,
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
            $store = \Mage::app()->getStore($storeId);
            $html.= '<strong>' . \Mage::app()->getLocale()->currency($store->getBaseCurrencyCode())->getSymbol() . '</strong>';
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

