<?php
/**
 * Hash Optimized option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Resource\Rule\Grid\Options;

class HashOptimized
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Tax\Model\Resource\Calculation\Rate\Collection
     */
    protected $_collection;

    /**
     * @param \Magento\Tax\Model\Resource\Calculation\Rate\Collection $collection
     */
    public function __construct(\Magento\Tax\Model\Resource\Calculation\Rate\Collection $collection)
    {
        $this->_collection = $collection;
    }

    /**
     * Return Hash Optimized array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_collection->toOptionHashOptimized();
    }
}
