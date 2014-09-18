<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Control;

use Magento\Ui\ViewInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Interface ActionPoolInterface
 */
interface ActionPoolInterface
{
    /**
     * Add button
     *
     * @param string $key
     * @param array $data
     * @param ViewInterface $context
     * @return void
     */
    public function add($key, array $data, ViewInterface $context);

    /**
     * Remove button
     *
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * Update button
     *
     * @param $key
     * @param array $data
     * @return void
     */
    public function update($key, array $data);
}
