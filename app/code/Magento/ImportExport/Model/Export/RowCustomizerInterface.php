<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ImportExport\Model\Export;

/**
 * Interface RowCustomizerInterface
 */
interface RowCustomizerInterface
{
    /**
     * Prepare data for export
     *
     * @param mixed $collection
     * @param int $productIds
     * @return mixed
     */
    public function prepareData($collection, $productIds);

    /**
     * Set headers columns
     *
     * @param array $columns
     * @return mixed
     */
    public function addHeaderColumns($columns);

    /**
     * Add data for export
     *
     * @param array $dataRow
     * @param int $productId
     * @return mixed
     */
    public function addData($dataRow, $productId);

    /**
     * Calculate the largest links block
     *
     * @param array $additionalRowsCount
     * @param int $productId
     * @return mixed
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId);

}
