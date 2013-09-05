<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Data\Collection;

class Factory
{
    /**
     * Create data collection instance
     *
     * @return \Magento\Data\Collection
     */
    public function create()
    {
        return new \Magento\Data\Collection();
    }
}
