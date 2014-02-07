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
class Datetime extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $format = $this->_app->getLocale()->getDateTimeFormat(
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM
        );
        return $this->_app->getLocale()->date(intval($element->getValue()))->toString($format);
    }
}
