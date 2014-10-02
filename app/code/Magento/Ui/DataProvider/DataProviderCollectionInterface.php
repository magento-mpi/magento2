<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider;

/**
 * Interface DataProviderCollectionInterface
 */
interface DataProviderCollectionInterface extends DataProviderInterface
{
    /**
     * Add a filter to the data
     *
     * @param array $filter
     * @return void
     */
    public function addFilter(array $filter);

    /**
     * Get data
     *
     * @return \Magento\Framework\Object[]
     */
    public function getData();
}
