<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Pricing;

use Magento\Pricing\Adjustment\AdjustmentInterface;
use Magento\Pricing\Object\SaleableInterface;
use \Magento\Weee\Helper\Data as WeeeHelper;

/**
 * Weee pricing adjustment
 */
class Adjustment implements AdjustmentInterface
{
    const CODE = 'weee';

    /**
     * @var WeeeHelper
     */
    protected $weeeHelper;

    /**
     * @var int|null
     */
    protected $sortOrder;

    /**
     * @param WeeeHelper $weeeHelper
     * @param int $sortOrder
     */
    public function __construct(WeeeHelper $weeeHelper, $sortOrder = null)
    {
        $this->weeeHelper = $weeeHelper;
        $this->sortOrder = $sortOrder;
    }

    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        return self::CODE;
    }

    /**
     * @return bool
     */
    public function isIncludedInBasePrice()
    {
        return false;
    }

    public function isIncludedInDisplayPrice(SaleableInterface $object)
    {
        $type = $this->weeeHelper->typeOfDisplay($object);
        return in_array($type, [
            \Magento\Weee\Model\Tax::DISPLAY_INCL,
            \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR,
            4
        ]);
    }

    /**
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $object)
    {
        return $this->getAmount($object);
    }

    /**
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $object)
    {
        return $amount + $this->getAmount($object);
    }

    /**
     * @param SaleableInterface $object
     * @return float
     */
    protected function getAmount($object)
    {
        return $this->weeeHelper->getAmount($object);
    }

    /**
     * {@inheritdoc}
     */
    public function isExcludedWith($adjustmentCode)
    {
        return $adjustmentCode === 'tax';
    }

    /**
     * Get sort order position
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->weeeHelper->isTaxable() ? $this->sortOrder : -1;
    }
}
