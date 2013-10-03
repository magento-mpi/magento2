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
 * Range grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Range extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    public function getHtml()
    {
        $html = '<div class="range"><div class="range-line">'
            . '<input type="text" name="' . $this->_getHtmlName()
            . '[from]" id="'.$this->_getHtmlId() . '_from" placeholder="'
            . __('From') . '" value="' . $this->getEscapedValue('from')
            . '" class="input-text no-changes" '
            . $this->getUiId('filter', $this->_getHtmlName(), 'from') . '/></div>';
        $html .= '<div class="range-line">'
            . '<input type="text" name="' . $this->_getHtmlName() . '[to]" id="'
            . $this->_getHtmlId() . '_to" placeholder="'
            . __('To') . '" value="' . $this->getEscapedValue('to') . '" class="input-text no-changes" '
            . $this->getUiId('filter', $this->_getHtmlName(), 'to') . '/></div></div>';
        return $html;
    }

    public function getValue($index=null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }
        $value = $this->getData('value');
        if ((isset($value['from']) && strlen($value['from']) > 0)
            || (isset($value['to']) && strlen($value['to']) > 0)
        ) {
            return $value;
        }
        return null;
    }

    public function getCondition()
    {
        $value = $this->getValue();
        return $value;
    }
}
