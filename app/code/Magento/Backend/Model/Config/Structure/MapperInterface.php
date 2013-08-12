<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Converter Mapper Interface
 */
interface Magento_Backend_Model_Config_Structure_MapperInterface
{
    /**
     * Apply map
     *
     * @param array $data
     * @return array
     */
    public function map(array $data);
}
