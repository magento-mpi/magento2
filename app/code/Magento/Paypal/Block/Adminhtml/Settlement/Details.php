<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Settlement reports transaction details
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Adminhtml\Settlement;

class Details extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    /**
     * Block construction
     * Initialize titles, buttons
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = '';
        $this->_headerText = __('View Transaction Details');
        $this->_removeButton('reset')
            ->_removeButton('delete')
            ->_removeButton('save');
    }

    /**
     * Initialize form
     * @return \Magento\Paypal\Block\Adminhtml\Settlement\Details
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('form', 'Magento\Paypal\Block\Adminhtml\Settlement\Details\Form');
        return $this;
    }
}
