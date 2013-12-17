<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;

class CrosssellProducts extends AssignProducts
{
    /**
     * @var string
     */
    protected $assignType = 'crosssell';

    /**
     * @var array
     */
    protected $_products = array();

    /**
     * Init Data
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'assignType ' => $this->assignType,
        );
        /** @var  $type Related|Upsell */
        $type = 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\\' . ucfirst(strtolower($this->assignType));
        $productsArray = array();
        foreach ($this->_products as $key => $product) {
            /** @var $product \Magento\Catalog\Test\Fixture\Product */
            $productsArray['product_' . $key] = array(
                'sku' => $product->getProductSku(),
                'name' => $product->getProductName()
            );
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
