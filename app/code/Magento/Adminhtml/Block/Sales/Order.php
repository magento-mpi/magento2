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
 * Adminhtml sales orders block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales;

class Order extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = __('Orders');
        $this->_addButtonLabel = __('Create New Order');
        parent::_construct();
        if (!$this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->_removeButton('add');
        }
    }

    /**
     * Retrieve url for order creation
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/sales_order_create/start');
    }
}
