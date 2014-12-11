<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Reward\Controller\Customer;

class Info extends \Magento\Reward\Controller\Customer
{
    /**
     * Load reward by customer
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _getReward()
    {
        $reward = $this->_objectManager->create(
            'Magento\Reward\Model\Reward'
        )->setCustomer(
            $this->_getCustomer()
        )->setWebsiteId(
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()
        )->loadByCustomer();
        return $reward;
    }

    /**
     * Info Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('current_reward', $this->_getReward());
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Reward Points'));
        $this->_view->renderLayout();
    }
}
