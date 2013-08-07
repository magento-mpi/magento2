<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend system config datetime field renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form_Field_Datetime extends Mage_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        return Mage::app()->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
