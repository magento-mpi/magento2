<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

/**
 * Class Amount
 */
class AmountFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param AdjustmentComposite $adjustmentComposite
     * @param Object\SaleableInterface $saleableItem
     * @param float $amount
     * @return \Magento\Pricing\Amount
     */
    public function create(
        AdjustmentComposite $adjustmentComposite,
        Object\SaleableInterface $saleableItem,
        $amount
    ) {
        return $this->objectManager->create('Magento\Pricing\Amount', array(
            'adjustmentComposite' => $adjustmentComposite,
            'saleableItem' => $saleableItem,
            'amount' => $amount
        ));
    }
}
