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
 * Catalog Custom Options Config Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Form\Renderer\Config;

class DateFieldsOrder
    extends \Magento\Backend\Block\System\Config\Form\Field
{

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $_options = array(
            'd' => __('Day'),
            'm' => __('Month'),
            'y' => __('Year')
        );

        $element->setValues($_options)
            ->setClass('select-date')
            ->setName($element->getName() . '[]');
        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = array();
        }

        $_parts = array();
        $_parts[] = $element->setValue(isset($values[0]) ? $values[0] : null)->getElementHtml();
        $_parts[] = $element->setValue(isset($values[1]) ? $values[1] : null)->getElementHtml();
        $_parts[] = $element->setValue(isset($values[2]) ? $values[2] : null)->getElementHtml();

        return implode(' <span>/</span> ', $_parts);
    }
}
