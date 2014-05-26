<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;

use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;
use \Magento\Framework\Logger;
use \Magento\Framework\Exception\InputException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param LinksInitializer $linkInitializer
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param Logger $logger
     */
    public function __construct(
        LinksInitializer $linkInitializer,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Logger $logger
    ) {
        $this->linkInitializer = $linkInitializer;
        $this->productFactory = $productFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($productSku, array $assignedProducts, $type)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $productId = $product->getIdBySku($productSku);

        if (!$productId) {
            throw new InputException('There is no product with provided SKU');
        }
        $product->load($productId);

        $links = [];
        /** @var Data\LinkedProductEntity[] $assignedProducts*/
        foreach ($assignedProducts as $linkedProduct) {
            $data = $linkedProduct->__toArray();
            $links[$data[Data\LinkedProductEntity::ID]] = $data;
        }
        $this->linkInitializer->initializeLinks($product, [$type => $links]);
        try {
            $product->save();
        } catch (\Exception $exception) {
            $this->logger->logException($exception);
            throw new InputException('Invalid data provided for linked products');
        }
    }
}
