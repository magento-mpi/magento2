<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Adjustment\Factory as AdjustmentFactory;
use Magento\Pricing\Adjustment\AdjustmentInterface;

/**
 * Class AdjustmentComposite
 */
class AdjustmentComposite
{
    /**
     * @var AdjustmentFactory
     */
    protected $adjustmentFactory;

    /**
     * @var array
     */
    protected $adjustments;

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustmentInstances;

    /**
     * @param AdjustmentFactory $adjustmentFactory
     * @param array $adjustments
     */
    public function __construct(AdjustmentFactory $adjustmentFactory, $adjustments = [])
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->adjustments = $adjustments;
    }

    /**
     * @return Adjustment\AdjustmentInterface[]
     */
    public function getAdjustments()
    {
        if (!isset($this->adjustmentInstances)) {
            $this->adjustmentInstances = $this->createAdjustments();
            uasort($this->adjustmentInstances, [$this, 'sortAdjustments']);
        }
        return $this->adjustmentInstances;
    }

    /**
     * Instantiate adjustments
     *
     * @return Adjustment\AdjustmentInterface[]
     */
    protected function createAdjustments()
    {
        $adjustments = [];
        foreach ($this->adjustments as $code => $adjustmentData) {
            if (!isset($adjustments[$code])) {
                $adjustments[$code] = $this->createAdjustment($code);
            }
        }
        return $adjustments;
    }

    /**
     * Create adjustment by code
     *
     * @param string $adjustmentCode
     * @return AdjustmentInterface
     */
    protected function createAdjustment($adjustmentCode)
    {
        $adjustmentData = $this->adjustments[$adjustmentCode];
        return $this->adjustmentFactory->create(
            $adjustmentData['className'],
            [
                'sortOrder' => isset($adjustmentData['sortOrder']) ? (int) $adjustmentData['sortOrder'] : -1
            ]
        );
    }

    /**
     * Sort adjustments
     *
     * @param AdjustmentInterface $firstAdjustment
     * @param AdjustmentInterface $secondAdjustment
     * @return int
     */
    protected function sortAdjustments(AdjustmentInterface $firstAdjustment, AdjustmentInterface $secondAdjustment)
    {
        if ($firstAdjustment->getSortOrder() < 0) {
            return 1;
        }
        return $firstAdjustment->getSortOrder() < $secondAdjustment->getSortOrder() ? -1 : 1;
    }
}
