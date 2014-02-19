<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\App\RequestInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Cms\Model\Wysiwyg;
use Magento\Core\Model\Registry;
use Magento\Logger;

class Builder
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @param ProductFactory $productFactory
     * @param Logger $logger
     * @param Registry $registry
     * @param Wysiwyg\Config $wysiwygConfig
     */
    public function __construct(
        ProductFactory $productFactory,
        Logger $logger,
        Registry $registry,
        Wysiwyg\Config $wysiwygConfig
    ) {
        $this->productFactory = $productFactory;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * Build product based on user request
     *
     * @param RequestInterface $request
     * @return \Magento\Catalog\Model\Product
     */
    public function build(RequestInterface $request)
    {
        $productId  = (int)$request->getParam('id');
        /** @var $product \Magento\Catalog\Model\Product */
        $product    = $this->productFactory->create();
        $product->setStoreId($request->getParam('store', 0));

        $typeId = $request->getParam('type');
        if (!$productId && $typeId) {
            $product->setTypeId($typeId);
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (\Exception $e) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                $this->logger->logException($e);
            }
        }

        $setId = (int)$request->getParam('set');
        if ($setId) {
            $product->setAttributeSetId($setId);
        }

        $this->registry->register('product', $product);
        $this->registry->register('current_product', $product);
        $this->wysiwygConfig->setStoreId($request->getParam('store'));
        return $product;
    }
} 
