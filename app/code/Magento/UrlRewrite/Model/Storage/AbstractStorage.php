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
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;
use Magento\UrlRewrite\Service\V1\Data\Filter;

/**
 * Abstract db storage
 */
abstract class AbstractStorage implements StorageInterface
{
    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder $urlRewriteBuilder
     */
    public function __construct(UrlRewriteBuilder $urlRewriteBuilder)
    {
        $this->urlRewriteBuilder = $urlRewriteBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByFilter(Filter $filter)
    {
        $rows = $this->doFindAllByFilter($filter);

        $urlRewrites = [];
        foreach ($rows as $row) {
            $urlRewrites[] = $this->createUrlRewrite($row);
        }
        return $urlRewrites;
    }

    /**
     * Find all rows by specific filter. Template method
     *
     * @param Filter $filter
     * @return array
     */
    abstract protected function doFindAllByFilter($filter);

    /**
     * {@inheritdoc}
     */
    public function findByFilter(Filter $filter)
    {
        $row = $this->doFindByFilter($filter);

        return $row ? $this->createUrlRewrite($row) : null;
    }

    /**
     * Find row by specific filter. Template method
     *
     * @param Filter $filter
     * @return array
     */
    abstract protected function doFindByFilter($filter);

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
     * Create url rewrite object
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite($data)
    {
        return $this->urlRewriteBuilder->populateWithArray($data)->create();
    }
}
