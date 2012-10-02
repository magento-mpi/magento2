<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * SOAP WS-Security UsernameToken nonce & timestamp temporary storage model.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Soap_Security_UsernameToken_NonceStorage
{
    /**
     * Nonce time to life in seconds.
     */
    const NONCE_TTL = 600;

    /**
     * Nonce prefix in cache ID.
     */
    const NONCE_CACHE_ID_PREFIX = 'WEBAPI_NONCE_';

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheInstance;

    /**
     * Construct nonce storage object.
     * Optional cache instance could be passed in arguments.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->_cacheInstance = isset($options['cacheInstance'])
            ? $options['cacheInstance']
            : Mage::app()->getCacheInstance();
    }

    /**
     * Validate nonce and timestamp pair.
     * Write nonce to storage if it's valid.
     *
     * @param string $nonce
     * @param int $timestamp
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException
     * @throws Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException
     */
    public function validateNonce($nonce, $timestamp)
    {
        $timestamp = (int) $timestamp;
        if ($timestamp <= 0 || $timestamp <= (time() - self::NONCE_TTL) || $timestamp > time()) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException;
        }

        if ($this->_cacheInstance->load($this->getNonceCacheId($nonce)) == $timestamp) {
            throw new Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException;
        }

        $this->_cacheInstance->save($timestamp, $this->getNonceCacheId($nonce), array(), self::NONCE_TTL);
    }

    /**
     * Generate cache ID for given nonce.
     *
     * @param string $nonce
     * @return string
     */
    public function getNonceCacheId($nonce)
    {
        return hash('md5', self::NONCE_CACHE_ID_PREFIX . $nonce);
    }
}
