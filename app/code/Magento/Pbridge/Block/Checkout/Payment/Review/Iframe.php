<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Block\Checkout\Payment\Review;

class Iframe extends \Magento\Pbridge\Block\Iframe\AbstractIframe
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '400';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Pbridge\Model\Session $pbridgeSession
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Model\Address\Mapper $addressConverter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Pbridge\Model\Session $pbridgeSession,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\Address\Mapper $addressConverter,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $pbridgeSession,
            $pbridgeData,
            $httpContext,
            $addressRepository,
            $addressConverter,
            $data
        );
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlSuccess()
    {
        if ($this->_getData('redirect_url_success')) {
            return $this->_getData('redirect_url_success');
        }
        return $this->getUrl('magento_pbridge/pbridge/success', ['_current' => true, '_secure' => true]);
    }

    /**
     * Return redirect url for Payment Bridge application
     *
     * @return string
     */
    public function getRedirectUrlError()
    {
        if ($this->_getData('redirect_url_error')) {
            return $this->_getData('redirect_url_error');
        }
        return $this->getUrl('magento_pbridge/pbridge/error', ['_current' => true, '_secure' => true]);
    }

    /**
     * Getter.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     * Can include quote shipping and billing address if its required in payment processing
     *
     * @return string
     */
    public function getSourceUrl()
    {
        $requestParams = [
            'notify_url' => $this->getUrl('magento_pbridge/PbridgeIpn/'),
            'redirect_url_success' => $this->getRedirectUrlSuccess(),
            'redirect_url_error' => $this->getRedirectUrlError(),
            'request_gateway_code' => $this->getMethod()->getOriginalCode(),
            'token' => $this->_pbridgeSession->getToken(),
            'already_entered' => '1',
            'magento_payment_action' => $this->getMethod()->getConfigPaymentAction(),
            'css_url' => $this->getCssUrl(),
            'customer_id' => $this->getCustomerIdentifier(),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
            'client_ip' => $this->_request->getClientIp(false),
        ];

        $sourceUrl = $this->_pbridgeData->getGatewayFormUrl($requestParams, $this->getQuote());
        return $sourceUrl;
    }
}
