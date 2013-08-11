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
 * Backend model for attribute, UnitPrice version
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
class Saas_UnitPrice_Model_Entity_Backend_Unitprice_Unit
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function validate($object)
    {
        if ($object->getUnitPriceUse()) {
            $fromUnit = $object->getUnitPriceUnit();
            $toUnit = $object->getUnitPriceBaseUnit();
            // will throw Exception if no conversion rate is defined
            try {
                $this->getUnitPriceInstance()->getConversionRate($fromUnit, $toUnit);
            } catch (Exception $e) {
                Mage::throwException(
                    $e->getMessage() . "<br/>\n"
                        . __('The product settings were not saved')
                );
            }
        }
        return parent::validate($object);
    }

    public function getUnitPriceInstance()
    {
        return Mage::getSingleton('Saas_UnitPrice_Model_Unitprice');
    }

    public function getUnitPriceHelper()
    {
        return Mage::helper('Saas_UnitPrice_Helper_Data');
    }
}
