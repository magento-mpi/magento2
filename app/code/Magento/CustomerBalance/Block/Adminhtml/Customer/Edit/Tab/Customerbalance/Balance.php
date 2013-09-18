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

class Balance extends \Magento\Adminhtml\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get delete orphan balances button
     *
     * @return string
     */
    public function getDeleteOrphanBalancesButton()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        $balance = \Mage::getModel('Magento\CustomerBalance\Model\Balance');
        if ($balance->getOrphanBalancesCount($customer->getId()) > 0) {
            return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData(array(
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
        return $this->getUrl('*/customerbalance/deleteOrphanBalances', array(
            '_current' => true,
            'tab' => 'customer_info_tabs_customerbalance'
        ));
    }
}
