<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
