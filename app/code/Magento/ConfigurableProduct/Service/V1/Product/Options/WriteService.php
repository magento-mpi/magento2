<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Options;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\AttributeFactory as ConfigurableAttributeFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\CollectionFactory;
use Magento\ConfigurableProduct\Service\V1\Data\Option;
use Magento\ConfigurableProduct\Service\V1\Data\OptionConverter;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var ConfigurableAttributeFactory
     */
    protected $configurableAttributeFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Attribute collection factory
     *
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * Eav config
     *
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Data\OptionConverter
     */
    protected $optionConverter;
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $productType;

    /**
     * @param ProductRepository $productRepository
     * @param ConfigurableAttributeFactory $configurableAttributeFactory
     * @param CollectionFactory $collectionFactory
     * @param EavConfig $eavConfig
     * @param OptionConverter $optionConverter
     * @param Configurable $productType
     */
    public function __construct(
        ProductRepository $productRepository,
        ConfigurableAttributeFactory $configurableAttributeFactory,
        CollectionFactory $collectionFactory,
        EavConfig $eavConfig,
        OptionConverter $optionConverter,
        Configurable $productType
    ) {
        $this->productRepository = $productRepository;
        $this->configurableAttributeFactory = $configurableAttributeFactory;
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->optionConverter = $optionConverter;
        $this->productType = $productType;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, Option $option)
    {
        $product = $this->productRepository->get($productSku);
        $eavAttribute = $this->eavConfig->getAttribute(Product::ENTITY, $option->getAttributeId());

        /** @var $configurableAttribute \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute */
        $configurableAttribute = $this->configurableAttributeFactory->create();
        $configurableAttribute->loadByProductAndAttribute($product, $eavAttribute);
        if ($configurableAttribute->getId()) {
            throw new CouldNotSaveException('Product already has this option');
        }

        $product->setTypeId(ConfigurableType::TYPE_CODE);
        $product->setConfigurableAttributesData(array($option->__toArray()));
        $product->save();

        $configurableAttribute = $this->configurableAttributeFactory->create();
        $configurableAttribute->loadByProductAndAttribute($product, $eavAttribute);
        if (!$configurableAttribute->getId()) {
            throw new CouldNotSaveException('Could not save product option');
        }

        return $this->optionConverter->convert($configurableAttribute);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);

        $attributeCollection = $this->productType->getConfigurableAttributeCollection($product);
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $option */
        $option = $attributeCollection->getItemById($optionId);

        if ($option === null) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }
        $option->delete();

        return true;
    }

    /**
     * @param string $productSku
     * @return \Magento\Catalog\Model\Product
     * @throws \Magento\Webapi\Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);
        if (ConfigurableType::TYPE_CODE !== $product->getTypeId()) {
            throw new Exception(
                'Product with specified sku: "%1" is not a configurable product',
                Exception::HTTP_FORBIDDEN,
                Exception::HTTP_FORBIDDEN,
                [
                    $product->getSku()
                ]
            );
        }
        return $product;
    }
}
