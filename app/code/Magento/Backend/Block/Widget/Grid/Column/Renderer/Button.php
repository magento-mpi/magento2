<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

class Button
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render grid row
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
    {
        $buttonType = $this->getColumn()->getButtonType();
        $buttonClass = $this->getColumn()->getButtonClass();
        return '<button'
            . ($buttonType ? ' type="' . $buttonType . '"' : '')
            . ($buttonClass ? ' class="' . $buttonClass . '"' : '')
            .'>'
            . $this->getColumn()->getHeader()
            . '</button>';
    }
}
