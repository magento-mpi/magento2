<?php
/**
 * Reward History grid
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\History;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $this->getCollection()->addCustomerFilter($customerId);
        return parent::_prepareCollection();
    }
}
