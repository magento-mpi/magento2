<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Element\UiComponent;

/**
 * Interface DataProviderInterface
 * @package Magento\Framework\View\Element\UiComponent
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
