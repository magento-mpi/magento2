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
// TODO: structure layer knows about service layer(and version) (MAGETWO-25952)
use Magento\UrlRedirect\Service\V1\Data\Converter;
use Magento\UrlRedirect\Service\V1\Data\Filter;

/**
 * Abstract db storage
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @param Converter $converter
     */
    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
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
            $flatData[] = $this->converter->convertObjectToArray($url);
        }
        $this->doAddMultiple($flatData);
    }

    /**
     * Add multiple data to storage. Template method
     *
     * @param array $data
     * @return int
     */
    abstract protected function doAddMultiple($data);

    /**
     * Create url rewrite object
     *
     * @param array $data
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite($data)
    {
        return $this->converter->convertArrayToObject($data);
    }
}
