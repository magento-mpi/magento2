<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class ProductRepository
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product[]
     */
    protected $instances = array();

    /**
     * @param ProductFactory $productFactory
     */
    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    /**
     * Retrieve product instance by sku
     *
     * @param string $sku
     * @param boolean $editMode
     * @return Product
     * @throws NoSuchEntityException
     */
    public function get($sku, $editMode = false)
    {
        if (!isset($this->instances[$sku])) {
            $product = $this->productFactory->create()->loadByAttribute('sku', $sku);
            if (!$product || !$product->getId()) {
                throw new NoSuchEntityException('Requested product doesn\'t exist');
            }
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            $this->instances[$sku] = $product;
        }
        return $this->instances[$sku];
    }
}
