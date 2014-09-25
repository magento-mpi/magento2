<?php
/**
 * Product controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller;

use Magento\Catalog\Model\Product as ModelProduct;

class Product extends \Magento\Framework\App\Action\Action implements \Magento\Catalog\Controller\Product\View\ViewInterface
{
    /**
     * Initialize requested product object
     *
     * @return ModelProduct
     */
    protected function _initProduct()
    {
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId = (int)$this->getRequest()->getParam('id');

        $params = new \Magento\Framework\Object();
        $params->setCategoryId($categoryId);

        /** @var \Magento\Catalog\Helper\Product $product */
        $product = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        return $product->initProduct($productId, $this, $params);
    }
}
