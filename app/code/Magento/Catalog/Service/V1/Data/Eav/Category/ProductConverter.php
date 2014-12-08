<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Eav\Category;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Service\V1\Data\Converter;
use Magento\Catalog\Service\V1\Data\Eav\Category\Product as CategoryProduct;

/**
 * @codeCoverageIgnore
 */
class ProductConverter extends Converter
{
    /**
     * @var int|null
     */
    private $position;

    /**
     * Convert a product model to a product data entity
     *
     * @param ProductModel $productModel
     * @return CategoryProduct
     */
    public function createProductDataFromModel(ProductModel $productModel)
    {
        $this->_populateBuilderWithAttributes($productModel);
        $this->productBuilder->setPosition($this->position);
        $productDto = $this->productBuilder->create();
        $this->setPosition(null);
        return $productDto;
    }

    /**
     * @param int|null $position
     * @return void
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
