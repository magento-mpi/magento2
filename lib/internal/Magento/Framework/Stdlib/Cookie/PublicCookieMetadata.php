<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

/**
 * Class PublicCookieMetadata
 *
 */
class PublicCookieMetadata extends AbstractCookieMetaData
{
    /**#@+
     * Constant for metadata value key.
     */
    const KEY_SECURE = 'secure';
    const KEY_HTTP_ONLY = 'http_only';
    const KEY_DURATION = 'duration';
    /**#@-*/

    /**
     * Set the number of seconds until the cookie expires
     *
     * The cookie duration can be translated into an expiration date at the time the cookie is sent.
     *
     * @param int $duration Time in seconds.
     * @return void
     */
    public function setDuration($duration)
    {
        $this->set(self::KEY_DURATION, $duration);
    }

    /**
     * Get the number of seconds until the cookie expires
     *
     * The cookie duration can be translated into an expiration date at the time the cookie is sent.
     *
     * @return int|null Time in seconds.
     */
    public function getDuration()
    {
        return $this->get(self::KEY_DURATION);
    }

    /**
     * Set HTTPOnly flag
     *
     * @param bool $httpOnly
     * @return void;
     */
    public function setHttpOnly($httpOnly)
    {
        $this->set(self::KEY_HTTP_ONLY, $httpOnly);
    }

    /**
     * Get HTTPOnly flag
     *
     * @return bool|null
     */
    public function getHttpOnly()
    {
        return $this->get(self::KEY_HTTP_ONLY);
    }

    /**
     * Set whether the cookie is only available under HTTPS
     *
     * @param bool $secure
     * @return void;
     */
    public function setSecure($secure)
    {
        $this->set(self::KEY_SECURE, $secure);
    }

    /**
     * Get whether the cookie is only available under HTTPS
     *
     * @return bool|null
     */
    public function getSecure()
    {
        return $this->get(self::KEY_SECURE);
    }
}
