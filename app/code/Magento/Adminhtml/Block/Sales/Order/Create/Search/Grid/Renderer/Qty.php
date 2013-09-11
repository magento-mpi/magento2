<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Renderer for Qty field in sales create new order search grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create\Search\Grid\Renderer;

class Qty
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Input
{
    /**
     * Returns whether this qty field must be inactive
     *
     * @param   \Magento\Object $row
     * @return  bool
     */
    protected function _isInactive($row)
    {
        return $row->getTypeId() == \Magento\Catalog\Model\Product\Type\Grouped::TYPE_CODE;
    }

    /**
     * Render product qty field
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        // Prepare values
        $isInactive = $this->_isInactive($row);

        if ($isInactive) {
            $qty = '';
        } else {
            $qty = $row->getData($this->getColumn()->getIndex());
            $qty *= 1;
            if (!$qty) {
                $qty = '';
            }
        }

        // Compose html
        $html = '<input type="text" ';
        $html .= 'name="' . $this->getColumn()->getId() . '" ';
        $html .= 'value="' . $qty . '" ';
        if ($isInactive) {
            $html .= 'disabled="disabled" ';
        }
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . ($isInactive ? ' input-inactive' : '') . '" />';
        return $html;
    }
}
