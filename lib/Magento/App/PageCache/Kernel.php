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
        return $this->cache->load($this->identifier->getValue());
    }

    /**
     * @param \Magento\App\Response\Http $response
     */
    public function process(\Magento\App\Response\Http $response)
    {
        if (preg_match('/public/', $response->getHeader('Cache-Control')) && $response->getHttpResponseCode() == 200) {
//            $response->setNoCacheHeaders();
            $response->clearHeader('Set-Cookie');
            header_remove('Set-Cookie');
//            $this->cache->save($response, $this->identifier->getValue(), array(), $response->getTtl());
        }
    }
}