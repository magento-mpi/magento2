<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore()->getWebsiteId()
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
        $this->_view->getPage()->getConfig()->setTitle(__('Reward Points'));
        $this->_view->renderLayout();
    }
}
