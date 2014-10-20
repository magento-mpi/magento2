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

use \Magento\Catalog\Api\ProductTypeListInterface;
use \Magento\Catalog\Model\ProductTypes\ConfigInterface;

class ProductTypeList implements ProductTypeListInterface
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
     * @var \Magento\Catalog\Api\Data\ProductTypeInterfaceFactory
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
     * @param \Magento\Catalog\Api\Data\ProductTypeInterfaceFactory $productTypeFactory
     */
    public function __construct(
        ConfigInterface $productTypeConfig,
        \Magento\Catalog\Api\Data\ProductTypeInterfaceFactory $productTypeFactory
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
                    'key' => $productTypeData['name'],
                    'value' => $productTypeData['label']
                ));
            }
        }
        return $this->productTypes;
    }
}
