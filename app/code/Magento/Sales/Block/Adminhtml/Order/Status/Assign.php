<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Status;

class Assign extends \Magento\Backend\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml_order_status';
        $this->_mode       = 'assign';
        $this->_blockGroup = 'Magento_Sales';
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
