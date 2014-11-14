<?php
/**
 * Created by PhpStorm.
 * User: akasian
 * Date: 11/13/2014
 * Time: 11:28 AM
 */

namespace Magento\CatalogSearch\Model\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Resource;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\RequestInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Build base Query for Index
 */
class IndexBuilder
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @param Resource $resource
     */
    public function __construct(Resource $resource, ScopeConfigInterface $config)
    {
        $this->resource = $resource;
        $this->config = $config;
    }

    /**
     * Build index query
     *
     * @param RequestInterface $request
     * @return Select
     */
    public function build(RequestInterface $request)
    {
        $select = $this->getSelect()
            ->from(
                ['search_index' => $this->resource->getTableName($request->getIndex())],
                ['entity_id' => 'search_index.product_id']
            )
            ->joinLeft(
                ['category_index' => 'catalog_category_product_index'],
                'search_index.product_id = category_index.product_id'
                    . ' AND search_index.store_id = category_index.store_id',
                []
            );


        $isShowOutOfStock = $this->config->isSetFlag(
                'cataloginventory/options/show_out_of_stock',
                ScopeInterface::SCOPE_STORE
        );
        if ($isShowOutOfStock === false) {
            $select->joinLeft(
                ['stock_index' => 'cataloginventory_stock_status'],
                'search_index.product_id = stock_index.product_id'
                . ' AND stock_index.website_id = 1 AND stock_index.stock_id = 1',
                []
            )->where('stock_index.stock_status = ?', 1);
        }
        return $select;
    }

    /**
     * Get empty Select
     *
     * @return Select
     */
    private function getSelect()
    {
        return $this->resource->getConnection(Resource::DEFAULT_READ_RESOURCE)->select();
    }
}