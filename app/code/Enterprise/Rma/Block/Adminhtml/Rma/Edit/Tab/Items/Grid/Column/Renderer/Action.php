<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Action
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renders column
     *
     * Shows link in one row instead of select element in parent class
     *
     * @param Magento_Object $row
     * @return string
     */
    public function render(Magento_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if ( empty($actions) || !is_array($actions) ) {
            return '&nbsp;';
        }

        $out = '<input type="hidden" id="h' . $row->getId() . '" name="h' . $row->getId() . '" value="' . $row->getId()
            . '" class="rowId" />';
        $out .= '<input type="hidden" name="items[' . $row->getId() . '][order_item_id]" value="'
            . $row->getOrderItemId() . '" />';
        $separator = '';
        foreach ($actions as $action) {
            if (!(isset($action['status_depended'])
                && (($row->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_APPROVED)
                    ||($row->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_DENIED)
                    ||($row->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_REJECTED)))) {
                $out .= $separator . $this->_toLinkHtml($action, $row);
                $separator = '<span class="separator">|</span>';
            }
        }
        return $out;
    }
}
