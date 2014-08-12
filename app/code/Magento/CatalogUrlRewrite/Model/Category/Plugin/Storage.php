<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin;

use Magento\UrlRewrite\Model\StorageInterface;
// TODO: structure layer knows about service layer(and version) (@TODO: UrlRewrite)
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\Category\ProductFactory;
use Magento\UrlRewrite\Service\V1\Data\FilterInterface;

class Storage
{
    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var UrlRewrite
     */
    protected $urlRewrite;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @param Converter $converter
     * @param UrlRewrite $urlRewrite
     * @param ProductFactory $productFactory
     */
    public function __construct(Converter $converter, UrlRewrite $urlRewrite, ProductFactory $productFactory)
    {
        $this->converter = $converter;
        $this->urlRewrite = $urlRewrite;
        $this->productFactory = $productFactory;
    }

    /**
     * @param StorageInterface $object
     * @param callable $proceed
     * @param array $urls
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAddMultiple(
        StorageInterface $object,
        \Closure $proceed,
        array $urls
    ) {
        $proceed($urls);
        $params = [];
        /** @var UrlRewrite $url */
        foreach ($urls as $url) {
            if ($url->getEntityType() == ProductUrlRewriteGenerator::ENTITY_TYPE) {
                $data = $this->converter->convertObjectToArray($url);
                if (!$data['metadata']) {
                    unset($data['metadata']);
                }
                $params = array_merge($params, [$data]);
            }
        }
        if ($params) {
            $records = $this->urlRewrite->getResourceCollection()->searchByParams($params);
            foreach ($records as $record) {
                $record['metadata'] = $record['metadata'] ? unserialize($record['metadata']) : '';
                if (empty($record['metadata']['category_id'])) {
                    continue;
                }
                $this->productFactory->create(['data' => array(
                    'url_rewrite_id' => $record['url_rewrite_id'],
                    'category_id'    => $record['metadata']['category_id'],
                    'product_id'     => $record['entity_id'],
                )])->setDataChanges(true)->save();
            }
        }
    }

    /**
     * @param StorageInterface $object
     * @param FilterInterface $filter
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDeleteByFilter(StorageInterface $object, FilterInterface $filter)
    {
        $params = [];
        foreach ($filter->getFilter() as $column => $value) {
            $params[$column] = is_array($value) ? array_shift($value) : $value;
        }
        if ($params) {
            //TODO: UrlRewrite it should be service
            $records = $this->urlRewrite->getResourceCollection()->searchByParams($params);
            foreach ($records as $record) {
                $this->productFactory->create()->load($record['url_rewrite_id'])->delete();
            }
        }
    }
}
