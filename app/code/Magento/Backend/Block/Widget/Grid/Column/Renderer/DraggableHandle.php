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

class DraggableHandle extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render grid row
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        return '<span class="' .
            $this->getColumn()->getInlineCss() .
            '"></span>' .
            '<input type="hidden" name="entity_id" value="' .
            $row->getData(
                $this->getColumn()->getIndex()
            ) . '"/>' . '<input type="hidden" name="position" value=""/>';
    }
}
