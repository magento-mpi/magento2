<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reports resource helper interface
 */
namespace Magento\Reports\Model\Resource;

interface HelperInterface
{
    /**
     * Merge Index data
     *
     * @param string $mainTable
     * @param array $data
     * @param mixed $matchFields
     * @return string
     */
    public function mergeVisitorProductIndex($mainTable, $data, $matchFields);

    /**
     * Update rating position
     *
     * @param string $type day|month|year
     * @param string $column
     * @param string $mainTable
     * @param string $aggregationTable
     * @return \Magento\Framework\DB\Helper\AbstractHelper
     */
    public function updateReportRatingPos($type, $column, $mainTable, $aggregationTable);
}
