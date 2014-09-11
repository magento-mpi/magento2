<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

class Button extends \Magento\Ui\Listing\Block\Column\Renderer\AbstractRenderer
{
    /**
     * Render grid row
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        $buttonType = $this->getColumn()->getButtonType();
        $buttonClass = $this->getColumn()->getButtonClass();
        return '<button' .
            ($buttonType ? ' type="' .
            $buttonType .
            '"' : '') .
            ($buttonClass ? ' class="' .
            $buttonClass .
            '"' : '') .
            '>' .
            $this->getColumn()->getHeader() .
            '</button>';
    }
}
