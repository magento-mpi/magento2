<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order;

/**
 * Sales archive grids containers
 */
class Container extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
         return $this;
    }
}
