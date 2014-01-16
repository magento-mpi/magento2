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

class Action
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * Renders column
     *
     * Shows link in one row instead of select element in parent class
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function render(\Magento\Object $row)
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
                && (($row->getStatus() === \Magento\Rma\Model\Rma\Source\Status::STATE_APPROVED)
                    ||($row->getStatus() === \Magento\Rma\Model\Rma\Source\Status::STATE_DENIED)
                    ||($row->getStatus() === \Magento\Rma\Model\Rma\Source\Status::STATE_REJECTED)))) {
                $out .= $separator . $this->_toLinkHtml($action, $row);
                $separator = '<span class="separator">|</span>';
            }
        }
        return $out;
    }
}
