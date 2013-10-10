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
 * PayPal common payment info block
 * Uses default templates
 */
namespace Magento\Paypal\Block\Payment;

class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var \Magento\Paypal\Model\InfoFactory
     */
    protected $_paypalInfoFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Paypal\Model\InfoFactory $paypalInfoFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Paypal\Model\InfoFactory $paypalInfoFactory,
        array $data = array()
    ) {
        $this->_paypalInfoFactory = $paypalInfoFactory;
        parent::__construct($coreData, $context, $storeManager, $locale, $paymentConfig, $data);
    }

    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        if (\Magento\Paypal\Model\Config::getIsCreditCardMethod($this->getInfo()->getMethod())) {
            return parent::getCcTypeName();
        }
    }

    /**
     * Prepare PayPal-specific payment information
     *
     * @param \Magento\Object|array $transport
     * @return \Magento\Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $paypalInfo = $this->_paypalInfoFactory->create();
        if (!$this->getIsSecureMode()) {
            $info = $paypalInfo->getPaymentInfo($payment, true);
        } else {
            $info = $paypalInfo->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
