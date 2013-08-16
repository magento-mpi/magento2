<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_Widget_Grid_Column_Renderer_Button
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render grid row
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
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
