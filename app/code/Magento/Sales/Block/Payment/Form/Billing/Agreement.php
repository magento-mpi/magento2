<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Billing Agreement form block
 */
class Magento_Sales_Block_Payment_Form_Billing_Agreement extends Magento_Payment_Block_Form
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::payment/form/billing/agreement.phtml';

    /**
     * @var Magento_Sales_Model_Billing_AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Sales_Model_Billing_AgreementFactory $agreementFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Sales_Model_Billing_AgreementFactory $agreementFactory,
        array $data = array()
    ) {
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setTransportName(
            Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract::TRANSPORT_BILLING_AGREEMENT_ID
        );
    }

    /**
     * Retrieve available customer billing agreements
     *
     * @return array
     */
    public function getBillingAgreements()
    {
        $data = array();
        $quote = $this->getParentBlock()->getQuote();
        if (!$quote || !$quote->getCustomer()) {
            return $data;
        }
        $collection = $this->_agreementFactory->create()->getAvailableCustomerBillingAgreements(
            $quote->getCustomer()->getId()
        );

        foreach ($collection as $item) {
            $data[$item->getId()] = $item->getReferenceId();
        }
        return $data;
    }
}
