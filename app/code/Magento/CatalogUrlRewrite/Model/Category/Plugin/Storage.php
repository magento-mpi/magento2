<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin;

use Magento\CatalogUrlRewrite\Model\Category\ProductFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;

class Storage
{
    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var ProductFactory */
    protected $productFactory;

    /**
     * @param UrlFinderInterface $urlFinder
     * @param ProductFactory $productFactory
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        ProductFactory $productFactory
    ) {
        $this->urlFinder = $urlFinder;
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Magento\UrlRewrite\Model\StorageInterface $object
     * @param callable $proceed
     * @param array $urls
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundReplace(StorageInterface $object, \Closure $proceed, array $urls)
    {
        $proceed($urls);
        $toSave = [];
        foreach ($this->filterUrls($urls) as $record) {
            $metadata = $record->getMetadata();
            $toSave[] = [
                'url_rewrite_id' => $record->getUrlRewriteId(),
                'category_id' => $metadata['category_id'],
                'product_id' => $record->getEntityId()
            ];
        }
        if ($toSave) {
            $this->productFactory->create()->getResource()->saveMultiple($toSave);
        }
    }

    /**
     * @param \Magento\UrlRewrite\Model\StorageInterface $object
     * @param array $data
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDeleteByData(StorageInterface $object, array $data)
    {
        $toRemove = [];
        $records = $this->urlFinder->findAllByData($data);
        foreach ($records as $record) {
            $toRemove[] = $record->getUrlRewriteId();
        }
        if ($toRemove) {
            $this->productFactory->create()->getResource()->removeMultiple($toRemove);
        }
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urls
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    protected function filterUrls(array $urls)
    {
        $filteredUrls = [];
        /** @var UrlRewrite $url */
        foreach ($urls as $url) {
            $metadata = $url->getMetadata();
            if ($url->getEntityType() == ProductUrlRewriteGenerator::ENTITY_TYPE && !empty($metadata['category_id'])) {
                $filteredUrls[] = $url;
            }
        }
        $data = [];
        foreach ($filteredUrls as $url) {
            foreach ([UrlRewrite::REQUEST_PATH, UrlRewrite::STORE_ID] as $key) {
                $fieldValue = $url->getByKey($key);
                if (!isset($data[$key]) || !in_array($fieldValue, $data[$key])) {
                    $data[$key][] = $fieldValue;
                }
            }
        }
        return $data ? $this->urlFinder->findAllByData($data) : [];
    }
}
