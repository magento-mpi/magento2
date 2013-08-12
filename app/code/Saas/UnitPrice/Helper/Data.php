<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper for the unitprice extension.
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Mage_Core_Model_ModuleListInterface
     */
    protected $_moduleList;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_ModuleListInterface $moduleList
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_Core_Model_ModuleListInterface $moduleList)
    {
        parent::__construct($context);
        $this->_moduleList = $moduleList;
    }

    /**
     * Return the unitprice lable for the given product. If no unitprice is set return ''
     * Possible template "variables":
     *  {{unitprice}}            => the unitprice value
     *  {{product_amount}}        => the product amount
     *  {{product_unit}}        => the product unit, full format
     *  {{product_unit_short}}    => the product unit, short format
     *  {{reference_amount}}    => the reference amount
     *  {{reference_unit}}        => the reference unit, full format
     *  {{reference_unit_short}}=> the reference unit, short format
     *
     * @param Mage_Catalog_Model_Product $product
     * "STRING" = the string is used as a format template
     *
     * @return string
     */
    public function getUnitPriceLabel($product)
    {
        $productAmount = $product->getUnitPriceAmount();
        if (!$productAmount) {
            return '';
        }

        $this->_loadDefaultUnitPriceValues($product);

        $referenceAmount = $product->getUnitPriceBaseAmount();
        if (!$referenceAmount) {
            return '';
        }

        $productPrice = $product->getFinalPrice();
        if (!$productPrice) {
            return '';
        }

        if (!is_numeric($productAmount) || !is_numeric($referenceAmount) || !is_numeric($productPrice)) {
            return '';
        }

        $productUnit = $product->getUnitPriceUnit();
        $referenceUnit = $product->getUnitPriceBaseUnit();

        $productPrice = $this->getHelperModel('Mage_Tax_Helper_Data')
            ->getPrice($product, $productPrice, $this->getConfig('unit_price_incl_tax'));
        $basePriceModel = $this->getModel(
            'Saas_UnitPrice_Model_Unitprice',
            array('params' => array('reference_unit' => $referenceUnit, 'reference_amount' => $referenceAmount))
        );
        $basePrice = $basePriceModel->getUnitPrice($productAmount, $productUnit, $productPrice);

        $label = $this->__($this->getConfig('frontend_label'));
        $label = str_replace('{{unitprice}}', $this->currency($basePrice), $label);
        $label = str_replace('{{product_amount}}', $productAmount, $label);
        $label = str_replace('{{product_unit}}', $this->__($productUnit), $label);
        $label = str_replace('{{product_unit_short}}', $this->__($productUnit . ' short'), $label);
        $label = str_replace('{{reference_amount}}', $referenceAmount, $label);
        $label = str_replace('{{reference_unit}}', $this->__($referenceUnit), $label);
        $label = str_replace('{{reference_unit_short}}', $this->__($referenceUnit . ' short'), $label);

        return $label;
    }

    /**
     * Set the configuration default values on the product model.
     * Used when products already existed when the extension was installed.
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return UnitPrice_Helper_Data
     */
    protected function _loadDefaultUnitPriceValues($product)
    {
        $array = array('unit_price_base_amount', 'unit_price_unit', 'unit_price_base_unit');

        foreach ($array as $attributeCode) {
            if (!$product->getDataUsingMethod($attributeCode)) {
                $attribute = $this->getModel('Mage_Eav_Model_Entity_Attribute')
                    ->loadByCode('catalog_product', $attributeCode);
                $product->setDataUsingMethod($attributeCode, $attribute->getFrontend()->getValue($product));
            }
        }

        return $this;
    }

    /**
     * Retrieve model object
     *
     * @param   string $className
     * @param   array $arguments
     * @return  Mage_Core_Model_Abstract|false
     */
    public function getModel($className = '', $arguments = array())
    {
        return Mage::getModel($className, $arguments);
    }

    /**
     * Retrieve helper object
     *
     * @param string $className the helper name
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelperModel($className = '')
    {
        return Mage::helper($className);
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @return  mixed
     */
    public function currency($value)
    {
        return Mage_Core_Helper_Data::currency($value);
    }

    /**
     * Check if the script is called from the adminhtml interface
     *
     * @return boolean
     */
    public function inAdmin()
    {
        return Mage::app()->getStore()->isAdmin();
    }

    /**
     * Dump a variable to the logfile (defaults to hideprices.log)
     *
     * @param mixed $var
     * @param string $file
     */
    public function log($var, $file = null)
    {
        $file = isset($file) ? $file : 'unitprice.log';

        $var = print_r($var, 1);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $var = str_replace("\n", "\r\n", $var);
        }
        Mage::log($var, null, $file);
    }

    /**
     * Check if the extension has been disabled in the system configuration
     *
     * @return boolean
     */
    public function moduleActive()
    {
        return (bool) $this->getConfig('enable_ext');
    }

    /**
     * Return the config value for the passed key (current store)
     *
     * @param string $key
     *
     * @return string
     */
    public function getConfig($key)
    {
        $path = 'catalog/unitprice/' . $key;
        return Mage::getStoreConfig($path, Mage::app()->getStore());
    }

    /**
     * Check if the UnitPricePro extension is installed and active
     *
     * @return boolean
     */
    public function isUnitPriceProInstalledAndActive()
    {
        return !!$this->_moduleList->getModule('UnitPricePro');
    }
}
