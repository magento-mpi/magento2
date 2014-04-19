<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Pricing\Price;

use Magento\Pricing\Object\SaleableInterface;

/**
 * Bundle selection price factory
 */
class BundleSelectionFactory
{
    /**
     * Default selection class
     */
    const SELECTION_CLASS_DEFAULT = 'Magento\Bundle\Pricing\Price\BundleSelectionPriceInterface';

    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create Price object for particular product
     *
     * @param SaleableInterface $bundleProduct
     * @param SaleableInterface $selection
     * @param float $quantity
     * @param array $arguments
     * @throws \InvalidArgumentException
     * @return BundleSelectionPriceInterface
     */
    public function create(
        SaleableInterface $bundleProduct,
        SaleableInterface $selection,
        $quantity,
        array $arguments = []
    ) {
        $arguments['bundleProduct'] = $bundleProduct;
        $arguments['salableItem'] = $selection;
        $arguments['quantity'] = $quantity ? floatval($quantity) : 1.;
        $selectionPrice = $this->objectManager->create(self::SELECTION_CLASS_DEFAULT, $arguments);
        if (!$selectionPrice instanceof BundleSelectionPriceInterface) {
            throw new \InvalidArgumentException(
                get_class($selectionPrice) . ' doesn\'t implement BundleSelectionPriceInterface'
            );
        }
        return $selectionPrice;
    }
}
