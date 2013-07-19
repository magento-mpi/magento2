<?php
/**
 * Hash Optimized option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Resource_Rule_Grid_Options_HashOptimized
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Tax_Model_Resource_Calculation_Rate_Collection
     */
    protected $_collection;

    /**
     * @param Mage_Tax_Model_Resource_Calculation_Rate_Collection $collection
     */
    public function __construct(Mage_Tax_Model_Resource_Calculation_Rate_Collection $collection)
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
