<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller\Adminhtml\Reward\Rate;

class Edit extends \Magento\Reward\Controller\Adminhtml\Reward\Rate
{
    /**
     * Edit Action
     *
     * @return void
     */
    public function execute()
    {
        $rate = $this->_initRate();
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $rate->getRateId() ? sprintf("#%s", $rate->getRateId()) : __('New Reward Exchange Rate')
        );
        $this->_view->renderLayout();
    }
}
