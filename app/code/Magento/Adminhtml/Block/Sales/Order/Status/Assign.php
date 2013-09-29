<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Sales\Order\Status;

class Assign extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_controller = 'sales_order_status';
        $this->_mode       = 'assign';
        parent::_construct();
        $this->_updateButton('save', 'label', __('Save Status Assignment'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Assign Order Status to State');
    }
}
