<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

/**
 * Text grid column filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Text extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = '<div class="field-100"><input type="text" name="' .
            $this->_getHtmlName() .
            '" id="' .
            $this->_getHtmlId() .
            '" value="' .
            $this->getEscapedValue() .
            '" class="input-text no-changes"' .
            $this->getUiId(
                'filter',
                $this->_getHtmlName()
            ) . ' /></div>';
        return $html;
    }
}
