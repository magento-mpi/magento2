<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1\UrlManager;

use Magento\UrlRewrite\Service\V1\Data\FilterInterface;
use Magento\UrlRewrite\Service\V1\UrlMatcherInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\CatalogUrlRewrite\Model\Resource\Category\Product;

class ProductUrlManager implements UrlMatcherInterface
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @param Product $product
     * @param Converter $converter
     */
    public function __construct(Product $product, Converter $converter)
    {
        $this->product = $product;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilter(FilterInterface $filter)
    {
        $row = $this->product->findByFilter($filter);

        return $row ? $this->createUrlRewrite($row) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllByFilter(FilterInterface $filter)
    {
        $rows = $this->product->findAllByFilter($filter);

        $urlRewrites = [];
        foreach ($rows as $row) {
            $urlRewrites[] = $this->createUrlRewrite($row);
        }
        return $urlRewrites;
    }

    /**
     * Create url rewrite object
     *
     * @param array $data
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite
     */
    protected function createUrlRewrite($data)
    {
        return $this->converter->convertArrayToObject($data);
    }

    /**
     * {@inheritdoc}
     */
    public function match($requestPath, $storeId)
    {
        // TODO: Implement match() method.
    }

    /**
     * {@inheritdoc}
     */
    public function findByEntity($entityId, $entityType, $storeId)
    {
        // TODO: Implement findByEntity() method.
    }
}

 
