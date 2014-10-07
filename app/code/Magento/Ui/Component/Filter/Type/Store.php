<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Component\Filter\Type;

use Magento\Ui\Component\Filter\FilterAbstract;

/**
 * Class Store
 */
class Store extends FilterAbstract
{
    /**
     * Prepare component data
     *
     * @return void
     */
    public function prepare()
    {
        // no preparation is needed
    }

    /**
     * Get condition by data type
     *
     * @param string|array $value
     * @return array|null
     */
    public function getCondition($value)
    {
        return $value;
    }
}
