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
    /**
     * @var  int
     */
    private $duration;

    /**
     * @var  bool
     */
    private $httpOnly;

    /*
     * @var bool
     */
    private $secure;

    /**
     * Set the number of seconds until the cookie expires
     *
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * Get the number of seconds until the cookie expires
     *
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set HTTPOnly flag
     *
     * @param bool $httpOnly
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * Get HTTPOnly flag
     *
     * @return bool|null
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Set whether the cookie is only available under HTTPS
     *
     * @param bool $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * Get whether the cookie is only available under HTTPS
     *
     * @return bool|null
     */
    public function getSecure()
    {
        return $this->secure;
    }
}
