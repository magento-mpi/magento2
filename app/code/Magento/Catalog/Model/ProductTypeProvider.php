<?php
/**
 * Product type provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

use \Magento\Catalog\Api\ProductTypeProviderInterface;
use \Magento\Catalog\Model\ProductTypes\ConfigInterface;

class ProductTypeProvider implements ProductTypeProviderInterface
{
    /**
     * Product type configuration provider
     *
     * @var ConfigInterface
     */
    private $productTypeConfig;

    /**
     * Product type factory
     *
     * @var ProductTypeFactory
     */
    private $productTypeFactory;

    /**
     * List of product types
     *
     * @var array
     */
    private $productTypes;

    /**
     * @param ConfigInterface $productTypeConfig
     * @param ProductTypeFactory $productTypeFactory
     */
    public function __construct(
        ConfigInterface $productTypeConfig,
        ProductTypeFactory $productTypeFactory
    ) {
        $this->productTypeConfig = $productTypeConfig;
        $this->productTypeFactory = $productTypeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypes()
    {
        if (is_null($this->productTypes)) {
            $productTypes = array();
            foreach ($this->productTypeConfig->getAll() as $productTypeData) {
                $productTypes[] = $this->productTypeFactory->create(array(
                    'name' => $productTypeData['name'],
                    'label' => $productTypeData['label']
                ));
            }
        }
        return $this->productTypes;
    }
}
