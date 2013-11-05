<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento Block interface
 */
namespace Magento\View\Element;

/**
 * @package Magento\View
 */
interface RendererInterface
{
    /**
     * Produce html output using the given data source
     *
     * @param mixed $data
     * @return mixed
     */
    public function render($data);
}
