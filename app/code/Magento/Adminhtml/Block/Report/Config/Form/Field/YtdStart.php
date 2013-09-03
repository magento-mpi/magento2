<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dashboard Year-To-Date Month and Day starts Field Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Config_Form_Field_YtdStart extends Magento_Backend_Block_System_Config_Form_Field
{

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $_months = array();
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = Mage::app()->getLocale()
                ->date(mktime(null,null,null,$i))
                ->get(Zend_Date::MONTH_NAME);
        }

        $_days = array();
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0'.$i : $i;
        }

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $element->setName($element->getName() . '[]');

        $_monthsHtml = $element->setStyle('width:100px;')
            ->setValues($_months)
            ->setValue(isset($values[0]) ? $values[0] : null)
            ->getElementHtml();

        $_daysHtml = $element->setStyle('width:50px;')
            ->setValues($_days)
            ->setValue(isset($values[1]) ? $values[1] : null)
            ->getElementHtml();

        return sprintf('%s %s', $_monthsHtml, $_daysHtml);
    }
}
