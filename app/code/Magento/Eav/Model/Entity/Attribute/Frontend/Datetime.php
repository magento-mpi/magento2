<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Eav_Model_Entity_Attribute_Frontend_Datetime extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Retreive attribute value
     *
     * @param $object
     * @return mixed
     */
    public function getValue(\Magento\Object $object)
    {
        $data = '';
        $value = parent::getValue($object);
        $format = Mage::app()->getLocale()->getDateFormat(
            Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = Mage::getSingleton('Magento_Core_Model_LocaleInterface')->date($value, Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (Exception $e) {
                $data = Mage::getSingleton('Magento_Core_Model_LocaleInterface')->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

