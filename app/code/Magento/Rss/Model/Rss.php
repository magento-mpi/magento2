<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Model;

use Magento\Framework\App\Rss\DataProviderInterface;

/**
 * Auth session model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rss
{
    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(\Magento\Framework\App\CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        if (is_null($this->dataProvider)) {
            return array();
        }
        $cache = false;
        if ($this->dataProvider->getCacheKey()) {
            $cache = $this->cache->load($this->dataProvider->getCacheKey());
        }

        if ($cache) {
            return unserialize($cache);
        }

        $data = $this->dataProvider->getData();

        if ($this->dataProvider->getCacheKey() && $this->dataProvider->getCacheLifetime()) {
            $this->cache->save(
                serialize($data),
                $this->dataProvider->getCacheKey(),
                array('rss'),
                $this->dataProvider->getCacheLifetime()
            );
        }

        return $data;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return $this
     */
    public function setDataProvider(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    /**
     * @return string
     */
    public function createRssXml()
    {
        $rssFeedFromArray = \Zend_Feed::importArray($this->getFeeds(), 'rss');
        return $rssFeedFromArray->saveXML();
    }
}
