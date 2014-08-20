<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Model\Storage;

use Magento\Framework\App\Resource;
use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;

/**
 * Abstract db storage
 */
abstract class AbstractStorage implements StorageInterface
{
    /** @var UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param UrlRewriteBuilder $urlRewriteBuilder
     */
    public function __construct(UrlRewriteBuilder $urlRewriteBuilder)
    {
        $this->urlRewriteBuilder = $urlRewriteBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByData(array $data)
    {
        $rows = $this->doFindAllByData($data);

        $urlRewrites = [];
        foreach ($rows as $row) {
            $urlRewrites[] = $this->createUrlRewrite($row);
        }
        return $urlRewrites;
    }

    /**
     * Find all rows by specific filter. Template method
     *
     * @param array $data
     * @return array
     */
    abstract protected function doFindAllByData($data);

    /**
     * {@inheritdoc}
     */
    public function findOneByData(array $data)
    {
        $row = $this->doFindOneByData($data);

        return $row ? $this->createUrlRewrite($row) : null;
    }

    /**
     * Find row by specific filter. Template method
     *
     * @param array $data
     * @return array
     */
    abstract protected function doFindOneByData($data);

    /**
     * {@inheritdoc}
     */
    public function addMultiple(array $urls)
    {
        $flatData = [];
        foreach ($urls as $url) {
            $flatData[] = $url->toArray();
        }
        $this->doAddMultiple($flatData);
    }

    /**
     * Add multiple data to storage. Template method
     *
     * @param array $data
     * @return int
     * @throws DuplicateEntryException
     */
    abstract protected function doAddMultiple($data);

    /**
     * {@inheritdoc}
     */
    public function replace(array $urls)
    {
        if (!$urls) {
            return;
        }

        $this->deleteByData($this->createFilterDataBasedOnUrls($urls));

        $this->addMultiple($urls);
    }

    /**
     * Create url rewrite object
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite($data)
    {
        return $this->urlRewriteBuilder->populateWithArray($data)->create();
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param UrlRewrite[] $urls
     * @return array
     */
    protected function createFilterDataBasedOnUrls($urls)
    {
        $data = [];
        $uniqueKeys = [UrlRewrite::REQUEST_PATH, UrlRewrite::STORE_ID];
        foreach ($urls as $url) {
            foreach ($uniqueKeys as $key) {
                $fieldValue = $url->getByKey($key);

                if (!isset($data[$key]) || !in_array($fieldValue, $data[$key])) {
                    $data[$key][] = $fieldValue;
                }
            }
        }
        return $data;
    }
}
