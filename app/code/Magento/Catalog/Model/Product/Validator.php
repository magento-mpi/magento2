<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product;

class Validator
{
    /**
     * Validate product data
     *
     * @param Product $product
     * @param RequestInterface $request
     * @param \Magento\Framework\Object $response
     * @return array
     */
    public function validate(Product $product, RequestInterface $request, \Magento\Framework\Object $response)
    {
        return $product->validate();
    }
}
