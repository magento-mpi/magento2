<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Stdlib;

abstract class AbstractCookieMetaData
{
    /**
     * @var String
     */
    protected $domain;

    /**
     * @var String;
     */
    protected $path;

    /**
     * Set the domain for the cookie
     *
     * @param String $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return String|null
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set path of the cookie
     *
     * @param String $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return String|null
     */
    public function getPath()
    {
        return $this->path;
    }
}
