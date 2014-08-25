<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedImportExport\Model\Export;

use \Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;

class RowCustomizer implements RowCustomizerInterface
{
    /**
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function addHeaderColumns($columns)
    {
        $columns = array_merge(
            $columns,
            array(
                '_associated_sku',
                '_associated_default_qty',
                '_associated_position'
            )
        );
        return $columns;
    }

    /**
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        return $dataRow;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }
}
