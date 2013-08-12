<?php
/**
 * Authentication is used for signing messages so that the subscriber can authenticate their validity.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Outbound_AuthenticationInterface
{
    /**
     * Get authentication signature to add to the headers
     *
     * @param string                         $body
     * @param Magento_Outbound_UserInterface $user
     *
     * @return array Headers to add to message
     */
    public function getSignatureHeaders($body, Magento_Outbound_UserInterface $user);
}
