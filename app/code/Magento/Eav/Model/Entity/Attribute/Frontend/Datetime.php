<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Eav\Model\Entity\Attribute\Frontend;

class Datetime extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
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
        $format = \Mage::app()->getLocale()->getDateFormat(
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
        );

        if ($value) {
            try {
                $data = \Mage::getSingleton('Magento\Core\Model\LocaleInterface')->date($value, \Zend_Date::ISO_8601, null, false)->toString($format);
            } catch (\Exception $e) {
                $data = \Mage::getSingleton('Magento\Core\Model\LocaleInterface')->date($value, null, null, false)->toString($format);
            }
        }

        return $data;
    }
}

