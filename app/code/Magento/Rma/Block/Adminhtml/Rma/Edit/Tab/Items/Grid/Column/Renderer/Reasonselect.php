<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer;

class Reasonselect
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\Items\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders column as select when it is editable
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getEditableView(\Magento\Object $row)
    {
        /** @var $rmaItemAttribute \Magento\Rma\Model\Item\Attribute */
        $rmaItemAttribute = \Mage::getModel('\Magento\Rma\Model\Item\Form')
            ->setFormCode('default')
            ->getAttribute('reason_other');

        $selectName = 'items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']';
        $html = '<select name="' . $selectName . '" class="action-select reason required-entry">'
            . '<option value=""></option>';

        $selectedIndex = $row->getData($this->getColumn()->getIndex());
        foreach ($this->getColumn()->getOptions() as $val => $label){
            $selected = isset($selectedIndex) && $val == $selectedIndex ? ' selected="selected"' : '';
            $html .= '<option value="' . $val . '"' . $selected . '>' . $label . '</option>';
        }

        if ($rmaItemAttribute && $rmaItemAttribute->getId()) {
            $selected = $selectedIndex == 0 && $row->getReasonOther() != '' ? ' selected="selected"' : '';
            $html .= '<option value="other"' . $selected . '>' . $rmaItemAttribute->getStoreLabel() . '</option>';
        }

        $html .= '</select>';
        $html .= '<input type="text" '
            . 'name="items[' . $row->getId() . '][reason_other]" '
            . 'value="' . $this->escapeHtml($row->getReasonOther()) . '" '
            . 'maxlength="255" '
            . 'class="input-text ' . $this->getColumn()->getInlineCss() . '" '
            . 'style="display:none" />';

        return $html;
    }

    /**
     * Renders column as select when it is not editable
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    protected function _getNonEditableView(\Magento\Object $row)
    {
        /** @var $rmaItemAttribute \Magento\Rma\Model\Item\Attribute */
        $rmaItemAttribute = \Mage::getModel('\Magento\Rma\Model\Item\Form')
            ->setFormCode('default')
            ->getAttribute('reason_other');
        $value = $row->getData($this->getColumn()->getIndex());

        if ($value == 0 && $row->getReasonOther() != '') {
            $html = $rmaItemAttribute && $rmaItemAttribute->getId()
                ? $rmaItemAttribute->getStoreLabel() . ':&nbsp;'
                : '';

            if (strlen($row->getReasonOther()) > 18) {
                $html .= '<a class="item_reason_other">'
                    . $this->escapeHtml(substr($row->getReasonOther() , 0, 15)) . '...'
                    . '</a>';

                $html .= '<input type="hidden" '
                    . 'name="items[' . $row->getId() . '][' . $rmaItemAttribute->getAttributeCode() . ']" '
                    . 'value="' . $this->escapeHtml($row->getReasonOther()) . '" />';
            } else {
                $html .= $this->escapeHtml($row->getReasonOther());
            }
        } else {
            $html = $this->escapeHtml($this->_getValue($row));
        }

        return $html;
    }
}
