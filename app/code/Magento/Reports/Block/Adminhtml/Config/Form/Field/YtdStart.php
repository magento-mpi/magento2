<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Dashboard Year-To-Date Month and Day starts Field Renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class YtdStart extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $_months = array();
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = $this->_localeDate->date(mktime(null, null, null, $i))->get(\Zend_Date::MONTH_NAME);
        }

        $_days = array();
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0' . $i : $i;
        }

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $element->setName($element->getName() . '[]');

        $_monthsHtml = $element->setStyle(
            'width:100px;'
        )->setValues(
            $_months
        )->setValue(
            isset($values[0]) ? $values[0] : null
        )->getElementHtml();

        $_daysHtml = $element->setStyle(
            'width:50px;'
        )->setValues(
            $_days
        )->setValue(
            isset($values[1]) ? $values[1] : null
        )->getElementHtml();

        return sprintf('%s %s', $_monthsHtml, $_daysHtml);
    }
}
