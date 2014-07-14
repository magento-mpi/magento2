<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRedirect\Model\Storage;

use Magento\Framework\App\Resource;
use Magento\UrlRedirect\Model\StorageInterface;
use Magento\UrlRedirect\Model\Data\BuilderFactory;
use Magento\UrlRedirect\Model\Data\Filter;

/**
 * Abstract db storage
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var BuilderFactory
     */
    protected $builderFactory;

    /**
     * @param BuilderFactory $builderFactory
     */
    public function __construct(BuilderFactory $builderFactory)
    {
        $this->builderFactory = $builderFactory;
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
    public function add(array $data)
    {
        $this->doAdd($data);
    }

    /**
     * Add multiple data to storage. Template method
     *
     * @param array $data
     * @return int
     */
    abstract protected function doAdd($data);

    /**
     * Create url rewrite object
     *
     * @param array $data
     * @return \Magento\UrlRedirect\Model\Data\UrlRewrite
     */
    protected function createUrlRewrite($data)
    {
        return $this->builderFactory->create()->populateWithArray($data)->create();
    }
}
