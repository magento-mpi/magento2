<?php
/**
 * This class is capable of creating HMAC signature headers.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Authentication_Hmac implements Magento_Outbound_AuthenticationInterface
{
    /**
     * The name of the header which stores the HMAC signature for client verification
     */
    const HMAC_HEADER = 'Magento-HMAC-Signature';

    /**
     * The name of the header which identifies the domain of the sender to the client
     */
    const DOMAIN_HEADER = 'Magento-Sender-Domain';

    /**
     * 256 bit Secure Hash Algorithm is used by default
     */
    const SHA256_ALGORITHM = 'sha256';

    /** @var Magento_Core_Model_StoreManagerInterface  */
    private $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(Magento_Core_Model_StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * Get authentication signature to add to the headers
     *
     * @param string                         $body
     * @param Magento_Outbound_UserInterface $user
     *
     * @throws LogicException
     * @return array Headers to add to message
     */
    public function getSignatureHeaders($body, Magento_Outbound_UserInterface $user)
    {
        $secret = $user->getSharedSecret();
        if ('' === $secret || is_null($secret)) {
            throw new LogicException('The shared secret cannot be empty.');
        }

        // Add HMAC Signature
        $signature = hash_hmac(self::SHA256_ALGORITHM, $body, $secret);
        return array(self::DOMAIN_HEADER => $this->_getDomain(), self::HMAC_HEADER => $signature);
    }

    /**
     * An overridable method to get the domain name
     *
     * @return mixed
     */
    protected function _getDomain()
    {
        return parse_url($this->_storeManager->getSafeStore()
            ->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_WEB), PHP_URL_HOST);
    }
}
