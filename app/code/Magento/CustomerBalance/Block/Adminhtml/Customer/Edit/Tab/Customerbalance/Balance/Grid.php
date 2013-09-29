<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Balance;

class Grid extends
    \Magento\Backend\Block\Widget\Grid
{
    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Url $urlModel
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        array $data = array()
    ) {
        $this->_balanceFactory = $balanceFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_balanceFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}
