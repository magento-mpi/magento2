<?php
/**
 * Two-legged OAuth server.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Rest\Oauth;

class Server extends \Magento\Oauth\Model\Server
{
    /**
     * Construct server.
     *
     * @param \Magento\Webapi\Controller\Request\Rest $request
     * @param \Magento\Oauth\Model\Token\Factory $tokenFactory
     * @param \Magento\Webapi\Model\Acl\User\Factory $consumerFactory
     * @param \Magento\Oauth\Model\Nonce\Factory $nonceFactory
     */
    public function __construct(
        \Magento\Webapi\Controller\Request\Rest $request,
        \Magento\Oauth\Model\Token\Factory $tokenFactory,
        \Magento\Webapi\Model\Acl\User\Factory $consumerFactory,
        \Magento\Oauth\Model\Nonce\Factory $nonceFactory
    ) {
        parent::__construct($request, $tokenFactory, $consumerFactory, $nonceFactory);
    }

    /**
     * Authenticate two-legged REST request.
     *
     * @return \Magento\Webapi\Model\Acl\User
     */
    public function authenticateTwoLegged()
    {
        // get parameters from request
        $this->_fetchParams();

        // make generic validation of request parameters
        $this->_validateProtocolParams();

        // initialize consumer
        $this->_initConsumer();

        // validate signature
        $this->_validateSignature();

        return $this->_consumer;
    }
}
