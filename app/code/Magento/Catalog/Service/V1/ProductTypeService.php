<?php
/**
 * Product type service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Catalog\Service\V1\Data\ProductTypeBuilder;

class ProductTypeService implements ProductTypeServiceInterface
{
    /**
     * @var ConfigInterface
     */
    private $productTypeConfig;

    /**
     * @var ProductTypeBuilder
     */
    private $productTypeBuilder;

    /**
     * @param ConfigInterface $productTypeConfig
     * @param ProductTypeBuilder $productTypeBuilder
     */
    public function __construct(
        ConfigInterface $productTypeConfig,
        ProductTypeBuilder $productTypeBuilder
    ) {
        $this->productTypeConfig = $productTypeConfig;
        $this->productTypeBuilder = $productTypeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypes()
    {
        $productTypes = [];
        foreach ($this->productTypeConfig->getAll() as $productTypeData) {
            $productTypes[] = $this->productTypeBuilder->setName($productTypeData['name'])
                ->setLabel($productTypeData['label'])
                ->create();
        }
        return $productTypes;
    }
}
