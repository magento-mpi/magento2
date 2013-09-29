<?php
/**
 * Reward History grid
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\History;

class Grid
    extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Prepare grid collection object
     *
     * @return \Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward\History\Grid
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $this->getCollection()->addCustomerFilter($customerId);
        return parent::_prepareCollection();
    }
}
