<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Billing agreement information on Order success page
 */
namespace Magento\Paypal\Block\Checkout\Onepage\Success;

class BillingAgreement extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Return billing agreement information
     *
     * @return string
     */
    protected function _toHtml()
    {
        $agreementReferenceId = $this->_checkoutSession->getLastBillingAgreementReferenceId();
        $customerId = $this->_customerSession->getCustomerId();
        if (!$agreementReferenceId || !$customerId) {
            return '';
        }
        $agreement = $this->_agreementFactory->create()->load($agreementReferenceId, 'reference_id');
        if ($agreement->getId() && $customerId == $agreement->getCustomerId()) {
            $this->addData(array(
                'agreement_ref_id' => $agreement->getReferenceId(),
                'agreement_url'    => $this->getUrl(
                    'paypal/billing_agreement/view',
                    array('agreement' => $agreement->getId())
                )
            ));
            return parent::_toHtml();
        }
        return '';
    }
}
