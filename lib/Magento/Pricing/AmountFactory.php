<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

/**
 * Amount factory
 */
class AmountFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param AdjustmentComposite $adjustmentComposite
     * @param Object\SaleableInterface $saleableItem
     * @param float $amount
     * @return \Magento\Pricing\AmountInterface
     * @throws \UnexpectedValueException
     */
    public function create(
        AdjustmentComposite $adjustmentComposite,
        Object\SaleableInterface $saleableItem,
        $amount
    ) {
        $amountModel =  $this->objectManager->create(
            'Magento\Pricing\Amount',
            [
                'adjustmentComposite' => $adjustmentComposite,
                'saleableItem' => $saleableItem,
                'amount' => $amount
            ]
        );
        if (!$amountModel instanceof AmountInterface) {
            throw new \UnexpectedValueException(
                get_class($amountModel) . ' doesn\'t implement \Magento\Pricing\AmountInterface'
            );
        }
        return $amountModel;
    }
}
