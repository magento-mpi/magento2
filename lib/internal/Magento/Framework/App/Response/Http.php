<?php
/**
 * HTTP response
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Response;

class Http extends \Zend_Controller_Response_Http implements \Magento\Framework\App\ResponseInterface
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
     * @var \Magento\Framework\App\Http\Context
     */
    protected $context;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\Framework\App\Http\Context $context
     */
    public function __construct(\Magento\Stdlib\Cookie $cookie, \Magento\Framework\App\Http\Context $context)
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
     * @return void
     */
    public function sendVary()
    {
        $data = $this->context->getData();
        if (!empty($data)) {
            ksort($data);
            $vary = sha1(serialize($data));
            $this->cookie->set(self::COOKIE_VARY_STRING, $vary, null, '/');
        } else {
            $this->cookie->set(self::COOKIE_VARY_STRING, null, -1, '/');
        }
    }

    /**
     * Send the response, including all headers, rendering exceptions if so
     * requested.
     *
     * @return void
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
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setPublicHeaders($ttl)
    {
        if ($ttl < 0 || !preg_match('/^[0-9]+$/', $ttl)) {
            throw new \InvalidArgumentException('Time to live is a mandatory parameter for set public headers');
        }
        $this->setHeader('pragma', 'cache', true);
        $this->setHeader('cache-control', 'public, max-age=' . $ttl . ', s-maxage=' . $ttl, true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $ttl . ' seconds')), true);
    }

    /**
     * Set headers for private cache
     *
     * @param int $ttl
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setPrivateHeaders($ttl)
    {
        if (!$ttl) {
            throw new \InvalidArgumentException('Time to live is a mandatory parameter for set private headers');
        }
        $this->setHeader('pragma', 'cache', true);
        $this->setHeader('cache-control', 'private, max-age=' . $ttl, true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('+' . $ttl . ' seconds')), true);
    }

    /**
     * Set headers for no-cache responses
     *
     * @return void
     */
    public function setNoCacheHeaders()
    {
        $this->setHeader('pragma', 'no-cache', true);
        $this->setHeader('cache-control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $this->setHeader('expires', gmdate('D, d M Y H:i:s T', strtotime('-1 year')), true);
    }

    /**
     * @return string[]
     */
    public function __sleep()
    {
        return array('_body', '_exceptions', '_headers', '_headersRaw', '_httpResponseCode', 'context', 'cookie');
    }
}
