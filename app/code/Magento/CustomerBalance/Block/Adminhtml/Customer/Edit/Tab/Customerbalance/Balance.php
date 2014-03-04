<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance;

class Balance extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_balanceFactory = $balanceFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get delete orphan balances button
     *
     * @return string
     */
    public function getDeleteOrphanBalancesButton()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        $balance = $this->_balanceFactory->create();
        if ($balance->getOrphanBalancesCount($customer->getId()) > 0) {
            return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(array(
                'label'     => __('Delete Orphan Balances'),
                'onclick'   => 'setLocation(\'' . $this->getDeleteOrphanBalancesUrl() .'\')',
                'class'     => 'scalable delete',
            ))->toHtml();
        }
        return '';
    }

    /**
     * Get delete orphan balances url
     *
     * @return string
     */
    public function getDeleteOrphanBalancesUrl()
    {
        return $this->getUrl('adminhtml/customerbalance/deleteOrphanBalances', array(
            '_current' => true,
            'tab' => 'customer_info_tabs_customerbalance'
        ));
    }
}
