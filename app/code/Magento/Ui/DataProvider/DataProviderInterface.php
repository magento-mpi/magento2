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
}
