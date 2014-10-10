<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;

class CrosssellProducts extends AssignProducts
{
    /**
     * @var string
     */
    protected $assignType = 'crosssell';

    /**
     * @var array
     */
    protected $_products = [];

    /**
     * Init Data
     */
    protected function _initData()
    {
        $this->_dataConfig = [
            'assignType ' => $this->assignType,
        ];
        /** @var  $type Related|Upsell */
        $type = 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\\' . ucfirst(strtolower($this->assignType));
        $productsArray = [];
        foreach ($this->_products as $key => $product) {
            /** @var $product \Magento\Catalog\Test\Fixture\Product */
            $productsArray['product_' . $key] = [
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ];
        }
        $this->_data['fields'][$this->assignType . '_products']['value'] = $productsArray;
        $this->_data['fields'][$this->assignType . '_products']['group'] = $type::GROUP;

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogAssignProducts($this->_dataConfig, $this->_data);
    }

    /**
     * Set specified product to local data
     *
     * @param array $products
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->_products = $products;
        $this->_initData();
        return $this;
    }
}
