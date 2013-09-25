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
class Magento_CustomerBalance_Block_Account_Balance extends Magento_Core_Block_Template
{
    /**
     * @var Magento_CustomerBalance_Model_BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_CustomerBalance_Model_BalanceFactory $balanceFactory
     * @param Magento_Customer_Model_Session $session
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Model_BalanceFactory $balanceFactory,
        Magento_Customer_Model_Session $session,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
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
