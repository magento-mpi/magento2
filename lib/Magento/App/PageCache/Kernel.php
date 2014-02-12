<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

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
     * @param Cache $cache
     * @param Identifier $identifier
     */
    public function __construct(
        \Magento\App\PageCache\Cache $cache,
        \Magento\App\PageCache\Identifier $identifier
    ) {
        $this->cache = $cache;
        $this->identifier = $identifier;
    }

    /**
     * @return \Magento\App\Response\Http|false
     */
    public function load()
    {
        return unserialize($this->cache->load($this->identifier->getValue()));
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    public function process(\Magento\App\Response\Http $response)
    {
        $maxAge = 0;
        if (preg_match('/public.*s-maxage=(\d+)/', $response->getHeader('Cache-Control')['value'], $matches)) {
            $maxAge = $matches[1];
        }
        if ($maxAge) {
            $response->setNoCacheHeaders();
            $response->clearHeader('Set-Cookie');
            if (!headers_sent()) {
                header_remove('Set-Cookie');
            }
            if ($response->getHttpResponseCode() == 200) {
                $this->cache->save(serialize($response), $this->identifier->getValue(), array(), $maxAge);
            }
        }
    }
}
