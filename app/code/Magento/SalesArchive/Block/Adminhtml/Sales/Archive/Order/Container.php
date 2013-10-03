<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales archive grids containers
 *
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Archive\Order;

class Container extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return $this;
    }
}
