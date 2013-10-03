<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block
 *
 */
namespace Magento\CustomerBalance\Block\Account;

class Balance extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_session = $session;
        $this->_balanceFactory = $balanceFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retreive current customers balance in base currency
     *
     * @return float
     */
    public function getBalance()
    {
        $customerId = $this->_session->getCustomerId();
        if (!$customerId) {
            return 0;
        }

        $model = $this->_balanceFactory->create()
            ->setCustomerId($customerId)
            ->loadByCustomer();

        return $model->getAmount();
    }
}
