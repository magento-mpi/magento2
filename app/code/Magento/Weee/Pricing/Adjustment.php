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
     * Weee helper
     *
     * @var WeeeHelper
     */
    protected $weeeHelper;

    /**
     * Sort order
     *
     * @var int|null
     */
    protected $sortOrder;

    /**
     * Constructor
     *
     * @param WeeeHelper $weeeHelper
     * @param int $sortOrder
     */
    public function __construct(WeeeHelper $weeeHelper, $sortOrder = null)
    {
        $this->weeeHelper = $weeeHelper;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Get adjustment code
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        return self::CODE;
    }

    /**
     * Define if adjustment is included in base price
     *
     * @return bool
     */
    public function isIncludedInBasePrice()
    {
        return false;
    }

    /**
     * Define if adjustment is included in display price
     *
     * @param SaleableInterface $object
     * @return bool
     */
    public function isIncludedInDisplayPrice(SaleableInterface $object)
    {
        $type = $this->weeeHelper->typeOfDisplay($object);
        return in_array(
            $type,
            [
                \Magento\Weee\Model\Tax::DISPLAY_INCL,
                \Magento\Weee\Model\Tax::DISPLAY_INCL_DESCR,
                4
            ]
        );
    }

    /**
     * Extract adjustment
     *
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function extractAdjustment($amount, SaleableInterface $object)
    {
        return $this->getAmount($object);
    }

    /**
     * Apply adjustment
     *
     * @param float $amount
     * @param SaleableInterface $object
     * @return float
     */
    public function applyAdjustment($amount, SaleableInterface $object)
    {
        return $amount + $this->getAmount($object);
    }

    /**
     * Obtain amount
     *
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
