<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\App\Cache\State;

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
