<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Status_Grid_Column_Unassign extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * Add decorated action to column
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateAction');
    }

    /**
     * Decorate values to column
     *
     * @param string $value
     * @param Magento_Sales_Model_Order_Status $row
     * @param Magento_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateAction($value, $row, $column, $isExport)
    {
        $cell = '';
        $state = $row->getState();
        if (!empty($state)) {
            $url = $this->getUrl(
                '*/*/unassign',
                array('status' => $row->getStatus(), 'state' => $row->getState())
            );
            $label = __('Unassign');
            $cell = '<a href="' . $url . '">' . $label . '</a>';
        }
        return $cell;
    }
}
