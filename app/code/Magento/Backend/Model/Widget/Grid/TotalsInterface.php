<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Widget\Grid;

interface TotalsInterface
{
    /**
     * Return object contains totals for all items in collection
     *
     * @abstract
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Framework\Object
     */
    public function countTotals($collection);
}
