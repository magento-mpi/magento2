<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib;

class PublicCookieMetadata
{
    /**
     * @var  int
     */
    protected $duration;

    /**
     * @var  bool
     */
    protected $httpOnly;

    /*
     * @var bool
     */
    protected $secure;

    /**
     * Set expire time
     *
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
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
     * @return bool|null
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Set Whether the cookie is only available under HTTPS
     *
     * @param bool $secure
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @return bool|null
     */
    public function getSecure()
    {
        return $this->secure;
    }
}
