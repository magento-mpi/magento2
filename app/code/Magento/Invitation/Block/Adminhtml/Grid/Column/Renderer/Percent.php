<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml grid percent column renderer
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
class Magento_Invitation_Block_Adminhtml_Grid_Column_Renderer_Percent
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Number
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        if ($this->getColumn()->getEditable()) {
            return parent::render($row);
        }

        $value = $this->_getValue($row);

        $value = round($value, 2);

        return $value . ' %';
    }
}
