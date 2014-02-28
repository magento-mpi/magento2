<?php
/**
 * Configurable product validation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Model\Product\Validator;

use Magento\App\RequestInterface;
use Magento\App\ResponseInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Code\Plugin\InvocationChain;
use Magento\Event\Manager;
use Magento\Core\Helper;

class Plugin
{
    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Helper\Data
     */
    protected $coreHelper;

    /**
     * @param Manager $eventManager
     * @param ProductFactory $productFactory
     * @param Helper\Data $coreHelper
     */
    public function __construct(Manager $eventManager, ProductFactory $productFactory, Helper\Data $coreHelper)
    {
        $this->eventManager = $eventManager;
        $this->productFactory = $productFactory;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Validate product data
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return ResponseInterface
     */
    public function aroundValidate(array $arguments, InvocationChain $invocationChain)
    {
        /** @var Product $product */
        $product = $arguments[0];
        /** @var RequestInterface $request */
        $request = $arguments[1];
        /** @var \Magento\Object $response */
        $response = $arguments[2];
        $result = $invocationChain->proceed($arguments);
        $variationProducts = (array)$request->getPost('variations-matrix');
        if ($variationProducts) {
            $validationResult = $this->_validateProductVariations($product, $variationProducts, $request);
            if (!empty($validationResult)) {
                $response->setError(true)
                    ->setMessage(__('Some product variations fields are not valid.'))
                    ->setAttributes($validationResult);
            }
        }
        return $result;
    }

    /**
     * Product variations attributes validation
     *
     * @param Product $parentProduct
     * @param array $products
     * @param RequestInterface $request
     * @return array
     */
    protected function _validateProductVariations(Product $parentProduct, array $products, RequestInterface $request)
    {

        $this->eventManager->dispatch(
            'catalog_product_validate_variations_before',
            array('product' => $parentProduct, 'variations' => $products)
        );
        $validationResult = array();
        foreach ($products as $productData) {
            $product = $this->productFactory->create();
            $product->setData('_edit_mode', true);
            $storeId = $request->getParam('store');
            if ($storeId) {
                $product->setStoreId($storeId);
            }
            $product->setAttributeSetId($parentProduct->getAttributeSetId());
            $product->addData($productData);
            $product->setCollectExceptionMessages(true);
            $configurableAttribute = $this->coreHelper ->jsonDecode($productData['configurable_attribute']);
            $configurableAttribute = implode('-', $configurableAttribute);

            $errorAttributes = $product->validate();
            if (is_array($errorAttributes)) {
                foreach ($errorAttributes as $attributeCode => $result) {
                    if (is_string($result)) {
                        $key = 'variations-matrix-' . $configurableAttribute . '-' . $attributeCode;
                        $validationResult[$key] = $result;
                    }
                }
            }
        }
        return $validationResult;
    }
}
