<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->buttonList->remove('add');
        parent::_prepareLayout();
        return $this;
    }
}
