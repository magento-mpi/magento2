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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_System_Config_Form_Field_Datetime extends Magento_Backend_Block_System_Config_Form_Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $format = Mage::app()->getLocale()->getDateTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        return Mage::app()->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
