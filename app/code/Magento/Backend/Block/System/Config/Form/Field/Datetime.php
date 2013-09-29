<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend system config datetime field renderer
 */
class Magento_Backend_Block_System_Config_Form_Field_Datetime extends Magento_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $format = $this->_application->getLocale()->getDateTimeFormat(
            Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM
        );
        return $this->_application->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
