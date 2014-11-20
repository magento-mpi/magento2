<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Interface CollectionDataSourceInterface
 */
interface CollectionDataSourceInterface extends DataSourceInterface
{
    /**
     * @return SearchResultInterface
     */
    public function getResultCollection();
}
