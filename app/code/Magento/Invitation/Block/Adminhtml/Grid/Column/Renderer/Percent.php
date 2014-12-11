<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Adminhtml grid percent column renderer
 *
 */
namespace Magento\Invitation\Block\Adminhtml\Grid\Column\Renderer;

class Percent extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Number
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    public function render(\Magento\Framework\Object $row)
    {
        if ($this->getColumn()->getEditable()) {
            return parent::render($row);
        }

        $value = $this->_getValue($row);

        $value = round($value, 2);

        return $value . ' %';
    }
}
