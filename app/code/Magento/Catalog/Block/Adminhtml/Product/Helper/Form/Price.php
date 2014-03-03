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
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

class Price extends \Magento\Data\Form\Element\Text
{
    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @var Magneto_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\LocaleInterface
     */
    protected $_locale;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Tax\Helper\Data $taxData
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\LocaleInterface $locale,
        \Magento\Tax\Helper\Data $taxData,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_storeManager = $storeManager;
        $this->_taxData = $taxData;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addClass('validate-zero-or-greater');
    }

    /**
     * @return mixed
     */
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
            $store = $this->_storeManager->getStore($storeId);
            $html .= '<strong>' . $this->_locale->currency($store->getBaseCurrencyCode())->getSymbol() . '</strong>';
            if ($this->_taxData->priceIncludesTax($store)) {
                if ($attribute->getAttributeCode()!=='cost') {
                    $addJsObserver = true;
                    $html .= ' <strong>[' . __('Inc. Tax') . '<span id="dynamic-tax-'
                        . $attribute->getAttributeCode() . '"></span>]</strong>';
                }
            }
        }
        if ($addJsObserver) {
            $html .= $this->_getTaxObservingCode($attribute);
        }

        return $html;
    }

    /**
     * @param mixed $attribute
     * @return string
     */
    protected function _getTaxObservingCode($attribute)
    {
        $spanId = "dynamic-tax-{$attribute->getAttributeCode()}";

        $html = "<script type='text/javascript'>if (dynamicTaxes == undefined) var dynamicTaxes = new Array(); dynamicTaxes[dynamicTaxes.length]='{$attribute->getAttributeCode()}'</script>";
        return $html;
    }

    /**
     * @param null|int|string $index
     * @return null|string
     */
    public function getEscapedValue($index=null)
    {
        $value = $this->getValue();

        if (!is_numeric($value)) {
            return null;
        }

        return number_format($value, 2, null, '');
    }
}
