<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Adminhtml\Order\Status;

class NewStatus extends \Magento\Adminhtml\Block\Widget\Form\Container
{

    protected function _construct()
    {
        $this->_objectId = 'status';
        $this->_controller = 'adminhtml_order_status';
        $this->_mode = 'new';

        parent::_construct();
        $this->_updateButton('save', 'label', __('Save Status'));
        $this->_removeButton('delete');
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('New Order Status');
    }
}
