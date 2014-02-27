<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

/**
 * Interface TableDataInterface
 * @package Magento\Catalog\Model\Indexer\Product\Flat
 */
interface TableDataInterface
{
    /**
     * Move data from temporary tables to flat
     *
     * @param string $flatTable
     * @param string $flatDropName
     * @param string $temporaryFlatTableName
     */
    public function move($flatTable, $flatDropName, $temporaryFlatTableName);
}
