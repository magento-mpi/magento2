<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow link iframe block
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Payflow\Link;

class Iframe extends \Magento\Paypal\Block\Iframe
{
    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set payment method code
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = \Magento\Paypal\Model\Config::METHOD_PAYFLOWLINK;
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflow/form', array('_secure' => true));
    }

    /**
     * Get secure token
     *
     * @return string
     */
    public function getSecureToken()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_token');
    }

    /**
     * Get secure token ID
     *
     * @return string
     */
    public function getSecureTokenId()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_token_id');
    }

    /**
     * Get payflow transaction URL
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return \Magento\Paypal\Model\Payflowlink::TRANSACTION_PAYFLOW_URL;
    }

    /**
     * Check sandbox mode
     *
     * @return bool
     */
    public function isTestMode()
    {
        $mode = $this->_paymentData
            ->getMethodInstance($this->_paymentMethodCode)
            ->getConfigData('sandbox_flag');
        return (bool) $mode;
    }
}
