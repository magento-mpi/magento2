<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Service\V1\Data\Product\OptionConverter;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;

class ReadService implements ReadServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var OptionConverter
     */
    private $optionConverter;
    /**
     * @var Type
     */
    private $type;

    /**
     * @param OptionConverter $optionConverter
     * @param ProductRepository $productRepository
     * @param Type $type
     */
    public function __construct(
        OptionConverter $optionConverter,
        ProductRepository $productRepository,
        Type $type
    ) {
        $this->optionConverter = $optionConverter;
        $this->productRepository = $productRepository;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        $optionCollection = $this->type->getOptionsCollection($product);
        $optionCollection->setIdFilter($optionId);

        /** @var \Magento\Bundle\Model\Option $optionDto */
        $optionDto = $optionCollection->getFirstItem();
        if (!$optionDto->getId()) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }
        return $optionDto;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $product = $this->getProduct($productSku);
        $optionCollection = $this->type->getOptionsCollection($product);

        /** @var \Magento\Bundle\Service\V1\Data\Product\Option[] $optionDtoList */
        $optionDtoList = [];
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            $optionDtoList[] = $this->optionConverter->createDataFromModel($option, $product);
        }
        return $optionDtoList;
    }

    /**
     * @param string $productSku
     * @return Product
     * @throws Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new Exception('Only implemented for bundle product', Exception::HTTP_FORBIDDEN);
        }

        return $product;
    }
}
