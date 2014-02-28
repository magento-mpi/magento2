<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

use Magento\Data\Form\Element\AbstractElement;

/**
 * Backend system config datetime field renderer
 */
class Notification extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setValue($this->_cache->load('admin_notifications_lastcheck'));
        $format = $this->_locale->getDateTimeFormat(
            \Magento\LocaleInterface::FORMAT_TYPE_MEDIUM
        );
        return $this->_locale->date(intval($element->getValue()))->toString($format);
    }
}
