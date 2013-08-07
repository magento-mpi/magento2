<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Magento_Data_Collection_Factory
{
    /**
     * Create data collection instance
     *
     * @return Magento_Data_Collection
     */
    public function create()
    {
        return new Magento_Data_Collection();
    }
}
