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
 * Dashboard Month-To-Date Day starts Field Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Config_Form_Field_MtdStart extends Magento_Backend_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $_days = array();
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0'.$i : $i;
        }

        $_daysHtml = $element->setStyle('width:50px;')
            ->setValues($_days)
            ->getElementHtml();

        return $_daysHtml;
    }
}
