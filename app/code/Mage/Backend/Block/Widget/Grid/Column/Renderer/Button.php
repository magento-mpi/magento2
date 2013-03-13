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
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $buttonType = $this->getColumn()->getButtonType();
        return '<button'
            . ($buttonType ? ' type="' . $buttonType . '"' : '')
            .'>'
            . $this->getColumn()->getHeader()
            . '</button>';
    }
}
