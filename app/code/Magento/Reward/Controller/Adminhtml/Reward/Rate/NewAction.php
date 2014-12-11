<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

class NewAction extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * New Action.
     * Forward to Edit Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
