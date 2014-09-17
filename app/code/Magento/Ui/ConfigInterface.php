<?php
/**
 * UI Library Configuration object interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui;

/**
 * Interface ConfigInterface
 */
interface ConfigInterface
{
    /**
     * Get configuration value by path
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getValue($key = '', $default = null);
}
