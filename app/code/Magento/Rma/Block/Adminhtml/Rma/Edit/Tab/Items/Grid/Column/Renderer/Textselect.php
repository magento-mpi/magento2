<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class Textselect extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders column as select when it is editable
     *
     * @param   \Magento\Framework\Object $row
     * @return  string
     */
    protected function _getEditableView(\Magento\Framework\Object $row)
    {
        $selectName = 'items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']';
        $html = '<select name="' . $selectName . '" class="action-select required-entry">';
        $value = $row->getData($this->getColumn()->getIndex());
        $html .= '<option value=""></option>';
        foreach ($this->getColumn()->getOptions() as $val => $label) {
            $selected = $val == $value && !is_null($value) ? ' selected="selected"' : '';
            $html .= '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
