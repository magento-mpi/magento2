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
namespace Magento\Adminhtml\Block\Report\Config\Form\Field;

class MtdStart extends \Magento\Backend\Block\System\Config\Form\Field
{

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
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
