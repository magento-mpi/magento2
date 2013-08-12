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
 * Frontend model for baseprice attribute
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default
    extends Magento_Eav_Model_Entity_Attribute_Frontend_Default
{

    /**
     * Returns default value based on store config
     *
     * @param Magento_Object $object
     * @return mixed
     */
    public function getValue(Magento_Object $object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (!isset($value) || !$value) {
            $value = $this->_getHelper()->getConfig(
                'default_'.$this->getAttribute()->getAttributeCode()
            );
        }
        return $value;
    }

    protected function _getHelper()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }
}
