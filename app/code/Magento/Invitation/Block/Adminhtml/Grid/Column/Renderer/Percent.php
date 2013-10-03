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
namespace Magento\Invitation\Block\Adminhtml\Grid\Column\Renderer;

class Percent
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Number
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
