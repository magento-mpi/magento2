<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\State;

interface OptionsInterface
{
    /**
     * Get all cache options
     *
     * @return array|bool
     */
    public function getAllOptions();

    /**
     * Save all options to option table
     *
     * @param  array $options
     * @return $this
     */
    public function saveAllOptions($options);
}
