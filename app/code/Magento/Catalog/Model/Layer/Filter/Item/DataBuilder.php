<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Item Data Builder
 */
namespace Magento\Catalog\Model\Layer\Filter\Item;

class DataBuilder
{
    /**
     * Array of items data
     * array(
     *      $index => array(
     *          'label' => $label,
     *          'value' => $value,
     *          'count' => $count
     *      )
     * )
     *
     * @return array
     */
    protected $_itemsData = [];

    /**
     * Add Item Data
     *
     * @param string $label
     * @param string $label
     * @param int $count
     */
    public function addItemData($label, $value, $count)
    {
        $this->_itemsData[] = array(
            'label' => $label,
            'value' => $value,
            'count' => $count
        );
    }

    /**
     * Get Items Data
     *
     * @param boolean $buildMoreThanOne
     */
    public function build($buildMoreThanOne = true)
    {
        $result = $this->_itemsData;
        if ($buildMoreThanOne && count($result) == 1) {
            $result = [];
        }
        $this->_itemsData = [];
        return $result;
    }
}
