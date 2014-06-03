<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

/**
 * Price model for external catalogs
 */
class CatalogPriceFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Provide custom price model with basic validation
     *
     * @param string $name
     * @return \Magento\Catalog\Model\Product\CatalogPriceInterface
     * @throws \UnexpectedValueException
     */
    public function create($name)
    {
        $customPriceModel = $this->objectManager->get($name);
        if (!$customPriceModel instanceof \Magento\Catalog\Model\Product\CatalogPriceInterface) {
            throw new \UnexpectedValueException(
                'Class ' . $name . ' should be an instance of \Magento\Catalog\Model\Product\CatalogPriceInterface'
            );
        }

        return $customPriceModel;
    }
}
