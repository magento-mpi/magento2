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
namespace Magento\Backend\Block\System\Config\Form\Field;

class Datetime extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $format = \Mage::app()->getLocale()->getDateTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        return \Mage::app()->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
