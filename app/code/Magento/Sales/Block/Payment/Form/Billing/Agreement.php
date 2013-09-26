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
namespace Magento\Sales\Block\Payment\Form\Billing;

class Agreement extends \Magento\Payment\Block\Form
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Sales::payment/form/billing/agreement.phtml';

    /**
     * @var \Magento\Sales\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Sales\Model\Billing\AgreementFactory $agreementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Sales\Model\Billing\AgreementFactory $agreementFactory,
        array $data = array()
    ) {
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setTransportName(
            \Magento\Sales\Model\Payment\Method\Billing\AbstractAgreement::TRANSPORT_BILLING_AGREEMENT_ID
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
