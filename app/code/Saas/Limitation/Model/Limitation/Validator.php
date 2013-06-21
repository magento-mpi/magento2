<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Limitation_Validator
{
    /**
     * Whether adding of a specified quantity of entities leads to exceeding a limitation threshold value
     *
     * @param Saas_Limitation_Model_Limitation_LimitationInterface $limitation
     * @param int $quantity
     * @return bool
     */
    public function exceedsThreshold(Saas_Limitation_Model_Limitation_LimitationInterface $limitation, $quantity = 1)
    {
        $threshold = $limitation->getThreshold();
        if ($threshold > 0) {
            return $limitation->getTotalCount() + $quantity > $threshold;
        }
        return false;
    }
}
