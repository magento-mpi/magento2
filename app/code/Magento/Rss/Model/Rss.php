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
     * @var array
     */
    protected $_feedArray = array();

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
     * @param array $data
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addHeader($data = array())
    {
        $this->_feedArray = $data;
        return $this;
    }

    /**
     * @param array $entries
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addEntries($entries)
    {
        $this->_feedArray['entries'] = $entries;
        return $this;
    }

    /**
     * @param array $entry
     * @return $this
     * @codeCoverageIgnore
     */
    public function _addEntry($entry)
    {
        $this->_feedArray['entries'][] = $entry;
        return $this;
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
