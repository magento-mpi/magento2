<?php
/**
 * HTTP response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Response;

class Http extends \Zend_Controller_Response_Http implements \Magento\App\ResponseInterface
{
    /**
     * Cookie to store page vary string
     */
    const COOKIE_VARY_STRING = 'X-Magento-Vary';

    /**
     * Response vary identifiers
     *
     * @var array
     */
    protected $vary;

    /**
     * Get header value by name.
     * Returns first found header by passed name.
     * If header with specified name was not found returns false.
     *
     * @param string $name
     * @return array|bool
     */
    public function getHeader($name)
    {
        foreach ($this->_headers as $header) {
            if ($header['name'] == $name) {
                return $header;
            }
        }
        return false;
    }

    /**
     * Set vary identifier
     *
     * @param string $name
     * @param string|array $value
     * @return $this
     */
    public function setVary($name, $value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        $this->vary[$name] = $value;
        if (!headers_sent()) {
            setcookie(self::COOKIE_VARY_STRING, $this->getVaryString(), null, '/');
        }
        return $this;
    }

    /**
     * Returns hash of varies
     *
     * @return string
     */
    public function getVaryString()
    {
        if (!empty($this->vary)) {
            ksort($this->vary);
        }

        return sha1(serialize($this->vary));
    }

    /**
     * Set headers for public cache
     * Accepts the time-to-live (max-age) parameter
     *
     * @param int $ttl
     * @throws \InvalidArgumentException
     */
    public function setPublicHeaders($ttl)
    {
        if (!$ttl) {
            throw new \InvalidArgumentException('time to live is a mandatory parameter for set public headers');
        }
        $this->setHeader('pragma', 'cache', true);
        $this->setHeader('cache-control', 'public, max-age=' . $ttl . ', s-maxage=' . $ttl, true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $ttl . ' seconds')), true);
    }

    /**
     * Set headers for private cache
     *
     * @param int $ttl
     * @throws \InvalidArgumentException
     */
    public function setPrivateHeaders($ttl)
    {
        if (!$ttl) {
            throw new \InvalidArgumentException('time to live is a mandatory parameter for set private headers');
        }
        $this->setHeader('pragma', 'cache', true);
        $this->setHeader('cache-control', 'private, max-age=' . $ttl, true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $ttl . ' seconds')), true);
    }

    /**
     * Set headers for no-cache responses
     */
    public function setNoCacheHeaders()
    {
        $this->setHeader('pragma', 'no-cache', true);
        $this->setHeader('cache-control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('-1 year')), true);
    }
}
