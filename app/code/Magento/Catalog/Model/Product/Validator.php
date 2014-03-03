<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

use Magento\App\RequestInterface;
use Magento\Catalog\Model\Product;

class Validator
{
    /**
     * Validate product data
     *
     * @param Product $product
     * @param RequestInterface $request
     * @param \Magento\Object $response
     * @return array
     */
    public function validate(Product $product, RequestInterface $request, \Magento\Object $response)
    {
        return $product->validate();
    }
}
