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
 * Observer for the Saas_UnitPrice extension.
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Observer
{
    /**
     * Set the default value on a product in the admin interface
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_UnitPrice_Model_Observer
     */
    public function catalogProductLoadAfter($observer)
    {
        if (!$this->_getSaasUnitPriceHelperData()->moduleActive()) {
            return $this;
        }

        $product = $observer->getProduct();
        foreach (array('unit_price_amount', 'unit_price_unit', 'unit_price_base_amount', 'unit_price_base_unit')
            as $attributeCode) {
            $data = $product->getDataUsingMethod($attributeCode);
            if (! isset($data)) {
                $attribute = $this->_getEavEntityAttributeModel();
                $attribute->loadByCode('catalog_product', $attributeCode);
                $product->setDataUsingMethod($attributeCode, $attribute->getFrontend()->getValue($product));
            }
        }

        return $this;
    }

    /**
     * Set the default values if BCP is installed and price updates are configured.
     * If BCP is not installed this event will never be fired.
     *
     * @param Magento_Event_Observer $observer
     */
    public function bcpUpdateDefaultsOnConfigurableProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $simpleProduct = $observer->getEvent()->getSimpleProduct();

        foreach (array('unit_price_amount', 'unit_price_unit', 'unit_price_base_amount', 'unit_price_base_unit')
            as $attributeCode) {

            $value = $simpleProduct->getDataUsingMethod($attributeCode);
            $product->setDataUsingMethod($attributeCode, $value);
        }
    }

    protected function _getSaasUnitPriceHelperData()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }


    protected function  _getEavEntityAttributeModel()
    {
        return Mage::getModel('Magento_Eav_Model_Entity_Attribute');
    }
}
