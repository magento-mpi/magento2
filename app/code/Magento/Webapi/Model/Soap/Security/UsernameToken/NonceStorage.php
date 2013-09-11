<?php
/**
 * Temporary storage of SOAP WS-Security username token nonce & timestamp.
 *
 * @see http://docs.oasis-open.org/wss-m/wss/v1.1.1/os/wss-UsernameTokenProfile-v1.1.1-os.html
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Security\UsernameToken;

class NonceStorage
{
    /**
     * Nonce time to life in seconds.
     */
    const NONCE_TTL = 600;

    /**
     * Acceptance time interval for nonce 'from future'. Helps to prevent errors due to time sync issues.
     */
    const NONCE_FROM_FUTURE_ACCEPTABLE_RANGE = 60;

    /**
     * Nonce prefix in cache ID.
     */
    const NONCE_CACHE_ID_PREFIX = 'WEBAPI_NONCE_';

    /**
     * @var \Magento\Core\Model\CacheInterface
     */
    protected $_cacheInstance;

    /**
     * Construct nonce storage object.
     *
     * @param \Magento\Core\Model\CacheInterface $cacheInstance
     */
    public function __construct(\Magento\Core\Model\CacheInterface $cacheInstance)
    {
        $this->_cacheInstance = $cacheInstance;
    }

    /**
     * Validate nonce and timestamp pair.
     * Write nonce to storage if it's valid.
     *
     * @param string $nonce
     * @param int $timestamp
     * @throws \Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException
     * @throws \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException
     */
    public function validateNonce($nonce, $timestamp)
    {
        $timestamp = (int)$timestamp;
        $isNonceUsed = $timestamp <= (time() - self::NONCE_TTL);
        $isNonceFromFuture = $timestamp > (time() + self::NONCE_FROM_FUTURE_ACCEPTABLE_RANGE);
        if ($timestamp <= 0 || $isNonceUsed || $isNonceFromFuture) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException;
        }

        if ($this->_cacheInstance->load($this->getNonceCacheId($nonce)) == $timestamp) {
            throw new \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException;
        }

        $nonceCacheTtl = self::NONCE_TTL + self::NONCE_FROM_FUTURE_ACCEPTABLE_RANGE;
        $this->_cacheInstance->save(
            $timestamp,
            $this->getNonceCacheId($nonce),
            array(\Magento\Webapi\Model\ConfigAbstract::WEBSERVICE_CACHE_TAG),
            $nonceCacheTtl
        );
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
