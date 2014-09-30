<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\DataProvider;

/**
 * Interface DataProviderInterface
 */
interface DataProviderInterface
{
    /**
     * Get meta data
     *
     * @return array
     */
    public function getMeta();

    /**
     * Get data
     *
     * @return array
     */
    public function getData();

    /**
     * Add a filter to the data
     *
     * @param array $filter
     * @return void
     */
    public function addFilter(array $filter);
}
