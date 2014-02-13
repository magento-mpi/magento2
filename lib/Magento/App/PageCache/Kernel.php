<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

/**
 * Builtin cache processor
 */
class Kernel
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Identifier
     */
    protected $identifier;

    /**
     * @var \Magento\App\Request\Http
     */
    protected $request;

    /**
     * @param Cache $cache
     * @param Identifier $identifier
     * @param \Magento\App\Request\Http $request
     */
    public function __construct(
        \Magento\App\PageCache\Cache $cache,
        \Magento\App\PageCache\Identifier $identifier,
        \Magento\App\Request\Http $request
    ) {
        $this->cache = $cache;
        $this->identifier = $identifier;
        $this->request = $request;
    }

    /**
     * Load response from cache
     *
     * @return \Magento\App\Response\Http|false
     */
    public function load()
    {
        if ($this->request->isGet() || $this->request->isHead()) {
            $response = unserialize($this->cache->load($this->identifier->getValue()));
        } else {
            $response = false;
        }
        return $response;
    }

    /**
     * Modify and cache application response
     *
     * @param \Magento\App\Response\Http $response
     */
    public function process(\Magento\App\Response\Http $response)
    {
        if (preg_match('/public.*s-maxage=(\d+)/', $response->getHeader('Cache-Control')['value'], $matches)) {
            $maxAge = $matches[1];
            $response->setNoCacheHeaders();
            if ($response->getHttpResponseCode() == 200 && ($this->request->isGet() || $this->request->isHead())) {
                $response->clearHeader('Set-Cookie');
                if (!headers_sent()) {
                    header_remove('Set-Cookie');
                }
                $this->cache->save(serialize($response), $this->identifier->getValue(), array(), $maxAge);
            }
        }
    }
}
