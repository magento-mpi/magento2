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
     * @var \Magento\Stdlib\Cookie
     */
    protected $cookie;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $context;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\App\Http\Context $context
     */
    public function __construct(\Magento\Stdlib\Cookie $cookie, \Magento\App\Http\Context $context)
    {
        $this->cookie = $cookie;
        $this->context = $context;
    }

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
     * Send Vary coookie
     */
    public function sendVary()
    {
        $data = array_filter($this->context->getData());
        if ($data) {
            ksort($data);
            $vary = sha1(serialize($data));
            $this->cookie->set(self::COOKIE_VARY_STRING, $vary, null, '/');
        }
    }

    /**
     * Send the response, including all headers, rendering exceptions if so
     * requested.
     */
    public function sendResponse()
    {
        $this->sendVary();
        parent::sendResponse();
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

    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_body', '_exceptions', '_headers', '_headersRaw', '_httpResponseCode');
    }
}
